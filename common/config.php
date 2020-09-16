<?php
define('SITE_TITLE', 'HRH Student');
define('TIME_ZONE', 'America/Cayman');
define('DATE_FORMAT', '%F');
define('TIME_FORMAT', '%I:%M%P');
define('CURRENCY_ENABLED', '0');
define('CURRENCY_POS', 'before'); // before or after
define('LANG_ENABLED', '0');
define('ADMIN_LANG_FILE', 'en.ini');
define('ENABLE_COOKIES_NOTICE', '1');
define('MAINTENANCE_MODE', '0');
define('MAINTENANCE_MSG', '<h1><i class=\"fa fa-rocket\"></i> Coming soon...</h1><p>We are sorry, our website is down for maintenance.</p>');
define('TEMPLATE', 'default');
define('OWNER', 'Dormitory Housing in Wisconsin Dells');
define('EMAIL', 'support@hiawatha.com');
define('ADDRESS', '200 West Hiawatha Drive, Wisconsin Dells, WI, USA');
define('PHONE', '(608) 253-0200');
define('MOBILE', '(608) 253-0200');
define('FAX', '');
define('DB_NAME', 'hrh_student');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', 'djwth10');  //diff

define('Document_Key', 'hAeBmHkkgRC6550WEm7QHyo3nSv7g4rx');
define('Sign_Url', 'http://192.168.0.53/signer/requests/sendAgreement');   //diff

define('SENDER_EMAIL', 'muellerUnity@gmail.com');
define('SENDER_NAME', 'Holtz Builders &lt;support@hiawatha.com&gt;');
define('USE_SMTP', '1');
define('SMTP_SECURITY', 'ssl');
define('SMTP_AUTH', '1');
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'muellerUnity@gmail.com');
define('SMTP_PASS', 'wpdkrdjwth22');
define('SMTP_PORT', '465');
define('GMAPS_API_KEY', '');
define('ANALYTICS_CODE', '<!-- Global site tag (gtag.js) - Google Analytics --><script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-177516312-1\"></script><script>  window.dataLayer = window.dataLayer || [];  function gtag(){dataLayer.push(arguments);}  gtag(\'js\', new Date());  gtag(\'config\', \'UA-177516312-1\');</script>');
define('ADMIN_FOLDER', 'admin');
define('CAPTCHA_PKEY', ''); // ReCAPTCHA public key
define('CAPTCHA_SKEY', ''); // ReCAPTCHA secret key
define('AUTOGEOLOCATE', '0'); // Change the currency according to the country (https required)
define('PAYMENT_TYPE', 'Credit Card,paypal'); // 2checkout,paypal,check,arrival
define('PAYPAL_EMAIL', 'sb-jzu1x3230523@business.example.com');

define('PAYMENT_TEST_MODE', '1');
define('ENABLE_DOWN_PAYMENT', '1');
define('DOWN_PAYMENT_RATE', '30'); // %
define('DOWN_PAYMENT_AMOUNT', '50'); // amount required to activate the down payment
define('ENABLE_TOURIST_TAX', '1');
define('TOURIST_TAX', '0');
define('TOURIST_TAX_TYPE', 'fixed');
define('ALLOW_COMMENTS', '1');
define('ALLOW_RATINGS', '1'); // If comments enabled
define('ENABLE_BOOKING_REQUESTS', '0'); // Possibility to make a reservation request if no availability
define('CURRENCY_CONVERTER_KEY', ''); // currencyconverterapi.com API key
define('ENABLE_ICAL', '1');
define('ENABLE_AUTO_ICAL_SYNC', '1');
define('ICAL_SYNC_INTERVAL', 'daily'); // daily | hourly
define('ICAL_SYNC_CLOCK', '3'); // 0-23h mode, required if ICAL_SYNC_INTERVAL = daily
