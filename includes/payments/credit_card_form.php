<!--payments/credit_card_form.php-->
<div class="creditCardForm">
    <div class="payment">
        <img src="<?php echo getFromTemplate('images/2checkout-cards.png'); ?>" alt="Credit Card" class="img-responsive mt10 mb30">
        <form action="<?php echo DOCBASE . $sys_pages['payment']['alias']; ?>" method="post">
            <input type="hidden" name="payment_confirm" value="credit_card_confirm">
            <input type="hidden" name="price" value="<?php echo str_replace(',', '.', $payed_amount); ?>">
            <input type="hidden" name="li_0_type" value="product">
            <input type="hidden" name="li_0_name" value="<?php echo addslashes(gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['from_date']).' > '.gmstrftime(DATE_FORMAT, $_SESSION['tmp_book']['to_date']).' - '.$_SESSION['tmp_book']['nights'].' '.$texts['NIGHTS'].' - '.($_SESSION['tmp_book']['adults']).' '.$texts['PERSONS']); ?>">

            <div class="form-group" id="card-number-field">
                <label for="cardNumber">Card Number</label>
                <input type="text" class="form-control" id="cardNumber" name="ccnumber" placeholder="" required="">
            </div>
            <div class="form-group CVV">
                <label data-toggle="tooltip" title=""
                       data-original-title="3 digits code on back side of the card">CVV </label>
                <input type="number" class="form-control" id="cvv" required="" name="cvv">
            </div>
            <div class="form-group" id="expiration-date">
                <label>Expiration Date</label>
                <select name="ccexpmm">
                    <option value="01">January</option>
                    <option value="02">February </option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
                <select name="ccexpyy">
                    <option value="20"> 2020</option>
                    <option value="21"> 2021</option>
                    <option value="22"> 2022</option>
                    <option value="23"> 2023</option>
                    <option value="24"> 2024</option>
                    <option value="25"> 2025</option>
                    <option value="26"> 2026</option>
                    <option value="27"> 2027</option>
                </select>
            </div>
            <div class="form-group" id="credit_cards">
                <button type="submit" name="submit" class="btn btn-primary btn-lg pull-right" id="confirm-purchase"><i class="fas fa-fw fa-credit-card"></i> <?php echo $texts['PAY']; ?></button>
            </div>
        </form>
    </div>
</div>