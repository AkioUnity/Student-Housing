<?php
require_once(SYSBASE . 'common/lib.php');
require_once(SYSBASE . 'common/define.php');
require_once('Nmi.php');

$gw = new Nmi();
$gw->setLogin("964bU2MK2taZqa9AQFKmDf2rBpQ85Mmf");

$gw->billing['firstname'] = $_SESSION['book']['firstname'];
$gw->billing['lastname'] = $_SESSION['book']['lastname'];
$gw->billing['address1'] = $_SESSION['book']['address'];

$gw->billing['city'] = $_SESSION['book']['city'];
$gw->billing['state'] = "";
$gw->billing['zip'] = $_SESSION['book']['postcode'];
$gw->billing['country'] = $_SESSION['book']['country'];
$gw->billing['phone'] = $_SESSION['book']['phone'];
$gw->billing['email'] = $_SESSION['book']['email'];

$gw->setOrder($_SESSION['tmp_book']['id'], $_POST['li_0_name'], 1, 1, "PO1234", "65.192.14.10");
$ccexp = $_POST['ccexpmm'] . $_POST['ccexpyy']; //"1010"  //999

$r = $gw->doSale($_POST['price'], $_POST['ccnumber'], $ccexp, $_POST['cvv']);
$response = $gw->responses;
if ($response['response'] == 1) {
    $payment_amount = $_POST['price'];
    $id_booking = $_SESSION['tmp_book']['id']; // $response['orderid']

    $result_booking = $db->query('SELECT * FROM pm_booking WHERE id = ' . $id_booking . ' AND status = 1 AND (trans IS NULL OR trans = \'\')');
    if ($result_booking !== false && $db->last_row_count() > 0) {

        $row = $result_booking->fetch();
        $expected_amount = (ENABLE_DOWN_PAYMENT == 1) ? $row['down_payment'] : $row['total'];

        $data = array();
        $data['id'] = $id_booking;
        $data['status'] = 4;
        $data['paid'] = $payment_amount;
        $data['balance'] = $row['total'] - $payment_amount;

        $result_booking = db_prepareUpdate($db, 'pm_booking', $data);
        if ($result_booking->execute() !== false) {

            $data = array();
            $data['id'] = null;
            $data['id_booking'] = $id_booking;
            $data['date'] = time();
            $data['trans'] = $response['transactionid'];
            $data['method'] = 'card';
            $data['amount'] = $payment_amount;

            $result_payment = db_prepareInsert($db, 'pm_booking_payment', $data);
            $result_payment->execute();

            $service_content = '';
            $result_service = $db->query('SELECT * FROM pm_booking_service WHERE id_booking = ' . $id_booking);
            if ($result_service !== false && $db->last_row_count() > 0) {
                foreach ($result_service as $service)
                    $service_content .= $service['title'] . ' x ' . $service['qty'] . ' : ' . formatPrice($service['amount'] * CURRENCY_RATE) . ' ' . $texts['INCL_VAT'] . '<br>';
            }

            $room_content = '';
            $result_room = $db->query('SELECT * FROM pm_booking_room WHERE id_booking = ' . $id_booking);
            if ($result_room !== false && $db->last_row_count() > 0) {
                foreach ($result_room as $room) {
                    $room_content .= '<p><b>' . $room['title'] . '</b><br>
                        ' . ($room['adults']) . ' ' . getAltText($texts['PERSON'], $texts['PERSONS'], ($room['adults']));
                    $room_content .= '<br>' . $texts['PRICE'] . ' : ' . formatPrice($room['amount'] * CURRENCY_RATE) . '</p>';
                }
            }

            $activity_content = '';
            $result_activity = $db->query('SELECT * FROM pm_booking_activity WHERE id_booking = ' . $id_booking);
            if ($result_activity !== false && $db->last_row_count() > 0) {
                foreach ($result_activity as $activity) {
                    $activity_content .= '<p><b>' . $activity['title'] . '</b> - ' . $activity['duration'] . ' - ' . strftime(DATE_FORMAT . ' ' . TIME_FORMAT, $activity['date']) . '<br>
                        ' . ($activity['adults']) . ' ' . getAltText($texts['PERSON'], $texts['PERSONS'], ($activity['adults']));
                    $activity_content .= $texts['PRICE'] . ' : ' . formatPrice($activity['amount'] * CURRENCY_RATE) . '</p>';
                }
            }

            $tax_content = '';
            $result_tax = $db->query('SELECT * FROM pm_booking_tax WHERE id_booking = ' . $id_booking);
            if ($result_tax !== false && $db->last_row_count() > 0) {
                foreach ($result_tax as $tax) {
                    $tax_content .= $tax['name'] . ': ' . formatPrice($tax['amount'] * CURRENCY_RATE) . '<br>';
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
                '{Check_in}' => gmstrftime(DATE_FORMAT, $row['from_date']),
                '{Check_out}' => gmstrftime(DATE_FORMAT, $row['to_date']),
                '{num_nights}' => $row['nights'],
                '{num_guests}' => ($row['adults']),
                '{rooms}' => $room_content,
                '{extra_services}' => $service_content,
                '{activities}' => $activity_content,
                '{comments}' => nl2br($row['comments']),
                '{tourist_tax}' => formatPrice($row['tourist_tax'] * CURRENCY_RATE),
                '{discount}' => '- ' . formatPrice($row['discount'] * CURRENCY_RATE),
                '{taxes}' => $tax_content,
                '{down_payment}' => formatPrice($row['down_payment'] * CURRENCY_RATE),
                '{total}' => formatPrice($row['total'] * CURRENCY_RATE),
                '{payment_notice}' => ''
            ));

            if ($mail !== false) {
                sendMail(SENDER_EMAIL, OWNER, $mail['subject'], $mail['content'], $row['email'], $row['firstname'] . ' ' . $row['lastname']);
                sendMail($row['email'], $row['firstname'] . ' ' . $row['lastname'], $mail['subject'], $mail['content']);
            }

            echo str_replace('{amount}', '<b>' . formatPrice($_POST['price'], DEFAULT_CURRENCY_SIGN) . ' ' . $texts['INCL_VAT'] . '</b>', '<h4>'.$texts['PAYMENT_ARRIVAL_NOTICE'].'</h4>');

//                        unset($_SESSION['book']);
        }
    }
} else {
//    print_r($response);
    echo '<h4>'.$response['responsetext'].'</h4>';
}
?>