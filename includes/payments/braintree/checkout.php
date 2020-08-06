<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');
require_once('init.php');

if(isset($_POST['payment_method_nonce'])){

	$result = Braintree_Transaction::sale([
		'amount' => $_POST['amount'],
		'paymentMethodNonce' => $_POST['payment_method_nonce'],
		'options' => [
			'submitForSettlement' => true
		]
	]);

	if($result->success){
		
		if(isset($_POST['id_booking'])&& is_numeric($_POST['id_booking'])){
			
			$id_booking = $_POST['id_booking'];
			$txn_id = $result->transaction->id;
			$payment_amount = $result->transaction->amount;
			$payment_currency = $result->transaction->currencyIsoCode;
		
			$result_booking = $db->query('SELECT * FROM pm_booking WHERE id = '.$id_booking.' AND status = 1 AND (trans IS NULL OR trans = \'\')');
			if($result_booking !== false && $db->last_row_count() > 0){
				
				$row = $result_booking->fetch();
		
                if($payment_currency == DEFAULT_CURRENCY_CODE
				&& ((ENABLE_DOWN_PAYMENT == 1 && $payment_amount == $row['down_payment']) || (ENABLE_DOWN_PAYMENT == 0 && $payment_amount == $row['total']))){
					
					$data['id'] = $id_booking;
					$data['status'] = 4;
					$data['payment_date'] = time();
					$data['trans'] = $txn_id;
					
					$result_booking = db_prepareUpdate($db, 'pm_booking', $data);
					if($result_booking->execute() !== false){
						
						$data = array();
						$data['id'] = null;
						$data['id_booking'] = $id_booking;
						$data['date'] = time();
						$data['trans'] = $txn_id;
						$data['method'] = 'braintree';
						$data['amount'] = $payment_amount;
						
						$result_payment = db_prepareInsert($db, 'pm_booking_payment', $data);
						$result_payment->execute();
						
						$service_content = '';
						$result_service = $db->query('SELECT * FROM pm_booking_service WHERE id_booking = '.$id_booking);
						if($result_service !== false && $db->last_row_count() > 0){
							foreach($result_service as $service)
								$service_content .= $service['title'].' x '.$service['qty'].' : '.formatPrice($service['amount']*CURRENCY_RATE).' '.$texts['INCL_VAT'].'<br>';
						}
						
						$room_content = '';
						$result_room = $db->query('SELECT * FROM pm_booking_room WHERE id_booking = '.$id_booking);
						if($result_room !== false && $db->last_row_count() > 0){
							foreach($result_room as $room){
								$room_content .= '<p><b>'.$room['title'].'</b><br>
								'.($room['adults']+$room['children']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($room['adults']+$room['children'])).': ';
								if($room['adults'] > 0) $room_content .= $room['adults'].' '.getAltText($texts['ADULT'], $texts['ADULTS'], $room['adults']).' ';
								if($room['children'] > 0){
                                    $room_content .= $room['children'].' '.getAltText($texts['CHILD'], $texts['CHILDREN'], $room['children']).' ';
                                    if(isset($room['child_age'])){
                                        $room_content .= '('.implode(' '.$texts['YO'].', ', $room['child_age']).' '.$texts['YO'].')';
                                    }
                                }
								$room_content .= '<br>'.$texts['PRICE'].' : '.formatPrice($room['amount']*CURRENCY_RATE).'</p>';
							}
						}
						
						$activity_content = '';
						$result_activity = $db->query('SELECT * FROM pm_booking_activity WHERE id_booking = '.$id_booking);
						if($result_activity !== false && $db->last_row_count() > 0){
							foreach($result_activity as $activity){
								$activity_content .= '<p><b>'.$activity['title'].'</b> - '.$activity['duration'].' - '.strftime(DATE_FORMAT.' '.TIME_FORMAT, $activity['date']).'<br>
								'.($activity['adults']+$activity['children']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($activity['adults']+$activity['children'])).': ';
								if($activity['adults'] > 0) $activity_content .= $activity['adults'].' '.getAltText($texts['ADULT'], $texts['ADULTS'], $activity['adults']).' ';
								if($activity['children'] > 0) $activity_content .= $activity['children'].' '.getAltText($texts['CHILD'], $texts['CHILDREN'], $activity['children']).' ';
								$activity_content .= $texts['PRICE'].' : '.formatPrice($activity['amount']*CURRENCY_RATE).'</p>';
							}
						}
						
						$tax_content = '';
						$result_tax = $db->query('SELECT * FROM pm_booking_tax WHERE id_booking = '.$id_booking);
						if($result_tax !== false && $db->last_row_count() > 0){
							foreach($result_tax as $tax){
								$tax_content .= $tax['name'].': '.formatPrice($tax['amount']*CURRENCY_RATE).'<br>';
							}
						}
						
						$mail = getMail($db, 'BOOKING_CONFIRMATION', array(
							'{firstname}' => $row['firstname'],
							'{lastname}' => $row['lastname'],
							'{company}' => $row['company'],
							'{address}' => $row['address'],
							'{postcode}' => $row['postcode'],
							'{city}' => $row['city'],
							'{country}' => $row['country'],
							'{phone}' => $row['phone'],
							'{mobile}' => $row['mobile'],
							'{email}' => $row['email'],
							'{Check_in}' => strftime(DATE_FORMAT, $row['from_date']),
							'{Check_out}' => strftime(DATE_FORMAT, $row['to_date']),
							'{num_nights}' => $row['nights'],
							'{num_guests}' => ($row['adults']+$row['children']),
							'{num_adults}' => $row['adults'],
							'{num_children}' => $row['children'],
							'{rooms}' => $room_content,
							'{extra_services}' => $service_content,
							'{activities}' => $activity_content,
							'{comments}' => nl2br($row['comments']),
							'{tourist_tax}' => formatPrice($row['tourist_tax']*CURRENCY_RATE),
							'{discount}' => '- '.formatPrice($row['discount']*CURRENCY_RATE),
							'{taxes}' => $tax_content,
							'{down_payment}' => formatPrice($row['down_payment']*CURRENCY_RATE),
							'{total}' => formatPrice($row['total']*CURRENCY_RATE),
							'{payment_notice}' => ''
						));
						
						if($mail !== false){
							$hotel_owners = array();
							$result_owner = $db->query('SELECT * FROM pm_user WHERE id IN ('.$row['users'].')');
							if($result_owner !== false){
								foreach($result_owner as $owner){
									if($owner['email'] != EMAIL)
										sendMail($owner['email'], $owner['firstname'], $mail['subject'], $mail['content'], $_SESSION['book']['email'], $_SESSION['book']['firstname'].' '.$_SESSION['book']['lastname']);
								}
							}
							sendMail(EMAIL, OWNER, $mail['subject'], $mail['content'], $row['email'], $row['firstname'].' '.$row['lastname']);
							sendMail($row['email'], $row['firstname'].' '.$row['lastname'], $mail['subject'], $mail['content']);
						}
						unset($_SESSION['book']);
						header('Location: '.DOCBASE.$sys_pages['booking']['alias'].'?action=confirm');// Payment has been authorised
						exit();
					}
				}
			}
		}
	}else{
		echo 'error';// Payment hasn't been authorised
	}
}elseif(isset($_POST['action']) && $_POST['action'] == 'generateclienttoken'){
	//$braintree_cust_id = "31904842";
	// Generate the nonce and send it back
	try
	{
		$clientToken = Braintree_ClientToken::generate(array(
			// use customerId to get a previous customer from the vault
			// 'customerId' => $braintree_cust_id    // $braintree_cust_id is Fetch from DB
		));
	}
	catch(Exception $e)
	{
		// cannot get the customer from the vault!!
		$clientToken = Braintree_ClientToken::generate();
	}
	
	echo $clientToken;
}
