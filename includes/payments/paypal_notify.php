<?php
require_once('../../common/lib.php');
require_once('../../common/define.php');

print_r($_POST);

if(isset($_POST['mc_gross'])){
    
    $req = 'cmd=_notify-validate';
    
    foreach($_POST as $key => $value){
        $value = urlencode(stripslashes($value));
        $req .= '&'.$key.'='.$value;
    }
    
    $host = (PAYMENT_TEST_MODE == 1) ? 'www.sandbox.paypal.com' : 'www.paypal.com';
    
    $header = 'POST /cgi-bin/webscr HTTP/1.1'."\r\n";
    $header .= 'Content-Type: application/x-www-form-urlencoded'."\r\n";
    $header .= 'Content-Length: '.strlen($req)."\r\n";
    $header .= 'Connection: close'."\r\n";
    $header .= 'Host: '.$host."\r\n\r\n";
    $fp = fsockopen('ssl://'.$host, 443, $errno, $errstr, 30);
    
    if($fp !== false){
        
        $payment_status = $_POST['payment_status'];
        $payment_amount = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $txn_id = $_POST['txn_id'];
        $receiver_email = $_POST['receiver_email'];
        $payer_email = $_POST['payer_email'];
        $id_booking = $_POST['custom'];
    
        fputs($fp, $header.$req);
        
        while(!feof($fp)){
            
            $res = fgets($fp, 1024);
            
            if(strcmp($res, 'VERIFIED') !== false){
                
                if($payment_status == 'Completed'){
                    
                    $result_booking = $db->query('SELECT * FROM pm_booking WHERE id = '.$id_booking.' AND status = 1 AND (trans IS NULL OR trans = \'\')');
                    if($result_booking !== false && $db->last_row_count() > 0){
                        
                        $row = $result_booking->fetch();
						
						$expected_amount = (ENABLE_DOWN_PAYMENT == 1) ? $row['down_payment'] : $row['total'];
                
                        if($receiver_email == PAYPAL_EMAIL && $payment_currency == DEFAULT_CURRENCY_CODE && $payment_amount == $expected_amount){
                            
							$data = array();
                            $data['id'] = $id_booking;
                            $data['status'] = 4;
                            $data['paid'] = $payment_amount;
                            $data['balance'] = $row['total']-$payment_amount;
                            
                            $result_booking = db_prepareUpdate($db, 'pm_booking', $data);
                            if($result_booking->execute() !== false){
                            
								$data = array();
								$data['id'] = null;
								$data['id_booking'] = $id_booking;
								$data['date'] = time();
								$data['trans'] = $txn_id;
								$data['method'] = 'paypal';
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
                                        '.($room['adults']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($room['adults'])).': ';
                                        $room_content .= '<br>'.$texts['PRICE'].' : '.formatPrice($room['amount']*CURRENCY_RATE).'</p>';
                                    }
                                }
                                
                                $activity_content = '';
                                $result_activity = $db->query('SELECT * FROM pm_booking_activity WHERE id_booking = '.$id_booking);
                                if($result_activity !== false && $db->last_row_count() > 0){
                                    foreach($result_activity as $activity){
                                        $activity_content .= '<p><b>'.$activity['title'].'</b> - '.$activity['duration'].' - '.strftime(DATE_FORMAT.' '.TIME_FORMAT, $activity['date']).'<br>
                                        '.($activity['adults']).' '.getAltText($texts['PERSON'], $texts['PERSONS'], ($activity['adults'])).': ';
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
                                    '{num_guests}' => ($row['adults']),
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
                                    sendMail(SENDER_EMAIL, OWNER, $mail['subject'], $mail['content'], $row['email'], $row['firstname'].' '.$row['lastname']);
                                    sendMail($row['email'], $row['firstname'].' '.$row['lastname'], $mail['subject'], $mail['content']);
                                }

                                echo str_replace('{amount}', '<b>' . formatPrice($_POST['price'], DEFAULT_CURRENCY_SIGN) . ' ' . $texts['INCL_VAT'] . '</b>', $texts['PAYMENT_ARRIVAL_NOTICE']);
                            }
                        }
                    }
                }
            }
        }
        fclose ($fp);
    }
}
