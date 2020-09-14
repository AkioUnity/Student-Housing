<?php
if (!isset($_SESSION['book']) || count($_SESSION['book']) == 0) {
    header('Location: ' . DOCBASE . $sys_pages['booking']['alias']);
    exit();
} else
    $_SESSION['book']['step'] = 'payment';

$msg_error = '';
$msg_success = '';
$field_notice = array();

if (isset($_SESSION['book']['id'])) {
    $result_booking = $db->query('SELECT * FROM pm_booking WHERE id = ' . $_SESSION['book']['id'] . ' AND status != 1 AND trans != \'\'');
    if ($result_booking !== false && $db->last_row_count() > 0) {
        unset($_SESSION['book']);
        header('Location: ' . DOCBASE . $sys_pages['booking']['alias']);
        exit();
    }
}

if (isset($_POST['payment_type'])) {
    $payment_type = $_POST['payment_type'];

    $total = $_SESSION['book']['total'];
    $payed_amount = (ENABLE_DOWN_PAYMENT == 1 && $_SESSION['book']['down_payment'] > 0) ? $_SESSION['book']['down_payment'] : $total;
    if (!isset($_SESSION['book']['id']) || is_null($_SESSION['book']['id'])) {
        $data = array();
        $data['id'] = null;
        $data['id_user'] = $_SESSION['book']['id_user'];
        $data['firstname'] = $_SESSION['book']['firstname'];
        $data['lastname'] = $_SESSION['book']['lastname'];
        $data['email'] = $_SESSION['book']['email'];
        $data['company'] = $_SESSION['book']['company'];
        $data['address'] = $_SESSION['book']['address'];
        $data['postcode'] = $_SESSION['book']['postcode'];
        $data['city'] = $_SESSION['book']['city'];
        $data['phone'] = $_SESSION['book']['phone'];
        $data['mobile'] = $_SESSION['book']['mobile'];
        $data['country'] = $_SESSION['book']['country'];
        $data['comments'] = $_SESSION['book']['comments'];
        $data['from_date'] = $_SESSION['book']['from_date'];
        $data['to_date'] = $_SESSION['book']['to_date'];
        $data['nights'] = $_SESSION['book']['nights'];
        $data['adults'] = $_SESSION['book']['adults'];
        $data['children'] = 0;
        //$data['tourist_tax'] = number_format($_SESSION['book']['tourist_tax'], 2, '.', '');
        $data['total'] = number_format($total, 2, '.', '');
        if ($payment_type != 'arrival') $data['down_payment'] = number_format($_SESSION['book']['down_payment'], 2, '.', '');
        $data['balance'] = $data['total'];
        $data['paid'] = 0;
        $data['add_date'] = time();
        $data['edit_date'] = null;
        $data['status'] = 1;
        $data['discount'] = number_format($_SESSION['book']['discount_amount'], 2, '.', '');
        $data['payment_option'] = $payment_type;
        $data['id_coupon'] = (isset($_SESSION['book']['id_coupon'])) ? $_SESSION['book']['id_coupon'] : null;

        $tax_amount = $_SESSION['book']['tax_rooms_amount'] + $_SESSION['book']['tax_activities_amount'] + $_SESSION['book']['tax_services_amount'];
        $data['tax_amount'] = number_format($tax_amount, 2, '.', '');
        $data['ex_tax'] = number_format($total - $tax_amount, 2, '.', '');

        $result_booking = db_prepareInsert($db, 'pm_booking', $data);
        if ($result_booking->execute() !== false) {

            $_SESSION['book']['id'] = $db->lastInsertId();

            if (isset($_SESSION['book']['sessid']))
                $db->query('DELETE FROM pm_room_lock WHERE sessid = ' . $db->quote($_SESSION['book']['sessid']));

            if (isset($_SESSION['book']['rooms']) && count($_SESSION['book']['rooms']) > 0) {
                foreach ($_SESSION['book']['rooms'] as $id_room => $rooms) {
                    foreach ($rooms as $index => $room) {
                        $data = array();
                        $data['id'] = null;
                        $data['id_booking'] = $_SESSION['book']['id'];
                        $data['id_room'] = $id_room;
                        $data['title'] = $room['title'];
                        $data['adults'] = $room['adults'];
                        $data['children'] = 0;
                        $data['amount'] = number_format($room['amount'], 2, '.', '');
                        if (isset($room['duty_free'])) $data['ex_tax'] = number_format($room['duty_free'], 2, '.', '');
                        if (isset($room['tax_rate'])) $data['tax_rate'] = $room['tax_rate'];

                        $result = db_prepareInsert($db, 'pm_booking_room', $data);
                        $result->execute();
                    }
                }
            }
            if (isset($_SESSION['book']['activities']) && count($_SESSION['book']['activities']) > 0) {
                foreach ($_SESSION['book']['activities'] as $id_activity => $activity) {
                    $data = array();
                    $data['id'] = null;
                    $data['id_booking'] = $_SESSION['book']['id'];
                    $data['id_activity'] = $id_activity;
                    $data['title'] = $activity['title'];
                    $data['adults'] = $activity['adults'];
                    $data['children'] = 0;
                    $data['duration'] = $activity['duration'];
                    $data['amount'] = number_format($activity['amount'], 2, '.', '');
                    $data['date'] = $activity['session_date'];
                    if (isset($activity['duty_free'])) $data['ex_tax'] = number_format($activity['duty_free'], 2, '.', '');
                    if (isset($activity['tax_rate'])) $data['tax_rate'] = $activity['tax_rate'];

                    $result = db_prepareInsert($db, 'pm_booking_activity', $data);
                    $result->execute();
                }
            }
            if (isset($_SESSION['book']['extra_services']) && count($_SESSION['book']['extra_services']) > 0) {
                foreach ($_SESSION['book']['extra_services'] as $id_service => $service) {
                    $data = array();
                    $data['id'] = null;
                    $data['id_booking'] = $_SESSION['book']['id'];
                    $data['id_service'] = $id_service;
                    $data['title'] = $service['title'];
                    $data['qty'] = $service['qty'];
                    $data['amount'] = number_format($service['amount'], 2, '.', '');
                    if (isset($service['duty_free'])) $data['ex_tax'] = number_format($service['duty_free'], 2, '.', '');
                    if (isset($service['tax_rate'])) $data['tax_rate'] = $service['tax_rate'];

                    $result = db_prepareInsert($db, 'pm_booking_service', $data);
                    $result->execute();
                }
            }
            if (isset($_SESSION['book']['taxes']) && count($_SESSION['book']['taxes']) > 0) {
                $tax_id = 0;
                $result_tax = $db->prepare('SELECT * FROM pm_tax WHERE id = :tax_id AND checked = 1 AND value > 0 AND lang = ' . LANG_ID . ' ORDER BY rank');
                $result_tax->bindParam(':tax_id', $tax_id);
                foreach ($_SESSION['book']['taxes'] as $tax_id => $taxes) {
                    $tax_amount = 0;
                    foreach ($taxes as $amount) $tax_amount += $amount;
                    if ($tax_amount > 0) {
                        if ($result_tax->execute() !== false && $db->last_row_count() > 0) {
                            $row = $result_tax->fetch();
                            $data = array();
                            $data['id'] = null;
                            $data['id_booking'] = $_SESSION['book']['id'];
                            $data['id_tax'] = $tax_id;
                            $data['name'] = $row['name'];
                            $data['amount'] = number_format($tax_amount, 2, '.', '');

                            $result = db_prepareInsert($db, 'pm_booking_tax', $data);
                            $result->execute();
                        }
                    }
                }
            }
            $_SESSION['tmp_book'] = $_SESSION['book'];
        }
    }

    if (isset($_SESSION['book']['id']) && $_SESSION['book']['id'] > 0) {
        $data = array();
        $data['id'] = $_SESSION['book']['id'];
        $data['payment_option'] = $payment_type;

        $result_booking = db_prepareUpdate($db, 'pm_booking', $data);
        $result_booking->execute();
    }

}elseif (isset($_POST['payment_confirm'])) {
    $payment_type = $_POST['payment_confirm'];
}
else {
    $payment_type = 'Credit Card,paypal';
}

/* ==============================================
 * CSS AND JAVASCRIPT USED IN THIS MODEL
 * ==============================================
 */

require(getFromTemplate('common/header.php', false)); ?>

<section id="page">
    <?php include(getFromTemplate('common/page_header.php', false)); ?>

    <div id="content" class="pt30 pb30">
        <div class="container">

            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>

            <div class="row mb30" id="booking-breadcrumb">
                <div class="col-sm-2 col-sm-offset-<?php echo (isset($_SESSION['tmp_book']['activities']) || isset($_SESSION['book']['activities'])) ? '1' : '2'; ?>">
                    <a href="<?php echo DOCBASE . $sys_pages['booking']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-calendar"></i>
                            <span><?php echo $sys_pages['booking']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <?php
                if (isset($_SESSION['tmp_book']['activities']) || isset($_SESSION['book']['activities'])) { ?>
                    <div class="col-sm-2">
                        <a href="<?php echo DOCBASE . $sys_pages['booking-activities']['alias']; ?>">
                            <div class="breadcrumb-item done">
                                <i class="fas fa-fw fa-ticket-alt"></i>
                                <span><?php echo $sys_pages['booking-activities']['name']; ?></span>
                            </div>
                        </a>
                    </div>
                    <?php
                } ?>
                <div class="col-sm-2">
                    <a href="<?php echo DOCBASE . $sys_pages['details']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-info-circle"></i>
                            <span><?php echo $sys_pages['details']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2">
                    <a href="<?php echo DOCBASE . $sys_pages['summary']['alias']; ?>">
                        <div class="breadcrumb-item done">
                            <i class="fas fa-fw fa-list"></i>
                            <span><?php echo $sys_pages['summary']['name']; ?></span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-2">
                    <div class="breadcrumb-item active">
                        <i class="fas fa-fw fa-credit-card"></i>
                        <span><?php echo $sys_pages['payment']['name']; ?></span>
                    </div>
                </div>
            </div>

            <?php echo $page['text']; ?>

            <?php
            if ($payment_type == 'paypal') { ?>
                <div class="text-center">
                    <?php echo $texts['PAYMENT_PAYPAL_NOTICE']; ?><br>
                    <img src="<?php echo getFromTemplate('images/paypal-cards.png'); ?>" alt="PayPal"
                         class="img-responsive mt10 mb30">
                    <form action="https://www.<?php if (PAYMENT_TEST_MODE == 1) echo 'sandbox.'; ?>paypal.com/cgi-bin/webscr"
                          method="post">
                        <input type='hidden' value="<?php echo str_replace(',', '.', $payed_amount); ?>" name="amount">
                        <input name="currency_code" type="hidden" value="<?php echo DEFAULT_CURRENCY_CODE; ?>">
                        <input name="shipping" type="hidden" value="0.00">
                        <input name="tax" type="hidden" value="0.00">
                        <input name="return" type="hidden"
                               value="<?php echo getUrl(true) . DOCBASE . $sys_pages['booking']['alias'] . '?action=confirm'; ?>">
                        <input name="cancel_return" type="hidden"
                               value="<?php echo getUrl(true) . DOCBASE . $sys_pages['booking']['alias'] . '?action=cancel'; ?>">
                        <input name="notify_url" type="hidden"
                               value="<?php echo getUrl(true) . DOCBASE . 'includes/payments/paypal_notify.php'; ?>">
                        <input name="cmd" type="hidden" value="_xclick">
                        <input name="business" type="hidden" value="<?php echo PAYPAL_EMAIL; ?>">
                        <input name="item_name" type="hidden"
                               value="<?php echo addslashes(gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['from_date']) . ' > ' . gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['to_date']) . ' - ' . $_SESSION['tmp_book']['nights'] . ' ' . $texts['NIGHTS'] . ' - ' . ($_SESSION['tmp_book']['adults']) . ' ' . $texts['PERSONS']); ?>">
                        <input name="no_note" type="hidden" value="1">
                        <input name="lc" type="hidden" value="<?php echo strtoupper(LANG_TAG); ?>">
                        <input name="bn" type="hidden" value="PP-BuyNowBF">
                        <input name="custom" type="hidden" value="<?php echo $_SESSION['tmp_book']['id']; ?>">

                        <button type="submit" name="submit" class="btn btn-primary btn-lg pull-right"><i
                                    class="fab fa-fw fa-paypal"></i> <?php echo $texts['PAY']; ?></button>
                    </form>
                </div>
                <?php
            } elseif ($payment_type == 'Credit Card') {
                include SYSBASE . 'includes/payments/credit_card_form.php';
            } elseif ($payment_type == 'credit_card_confirm') {
                include SYSBASE . 'includes/payments/credit_card_notify.php';
            } else { ?>
                <div class="text-center lead pt20 pb20">
                    <form method="post" action="<?php echo DOCBASE . $sys_pages['payment']['alias']; ?>">
                        <?php
                        $payments = array_map('trim', explode(',', PAYMENT_TYPE)); ?>
                        <div class="mb10">
                            <?php echo $texts['CHOOSE_PAYMENT']; ?>
                        </div>
                        <?php foreach ($payments as $payment) { ?>
                            <button type="submit" name="payment_type" class="btn btn-default"
                                    value="<?php echo $payment; ?>">
                                <?php
                                switch ($payment) {
                                    case 'Credit Card': ?>
                                        <i class="fas fa-fw fa-credit-card"></i><br>Credit Card
                                        <?php
                                        break;
                                    case 'paypal': ?>
                                        <i class="fab fa-fw fa-paypal"></i><br>PayPal
                                        <?php
                                        break;
                                } ?>
                            </button>
                        <?php } ?>
                    </form>
                </div>

                <div class="clearfix"></div>
                <a class="btn btn-default btn-lg pull-left"
                   href="<?php echo DOCBASE . $sys_pages['summary']['alias']; ?>"><i
                            class="fas fa-fw fa-angle-left"></i> <?php echo $texts['PREVIOUS_STEP']; ?></a>

                <?php
            } ?>
        </div>
    </div>
</section>

