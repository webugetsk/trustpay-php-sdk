<?php

require_once "../vendor/autoload.php";

$configuration = new \TrustPay\Configuration();
$configuration->setSuccessUrl("https://example.com/success");
$configuration->setErrorUrl("https://example.com/error");
$configuration->setCancelUrl("https://example.com/cancel");
$configuration->setNotificationUrl("https://example.com/notification");

$configuration->setAccountId("987654321");
$configuration->setSecret("abcd1234");

$configuration->setCurrency(\TrustPay\Enums\Currency::_EUR_);
$configuration->setLanguage(\TrustPay\Enums\Language::SK);

$trustPay = new \TrustPay\TrustPay($configuration);

$request = $trustPay->payment(
    0.01,
    "reference/Variable Symbol",
    "email.address-of-customer@example.com",
    null,
    "Payment description (Order ID 1234) etc... "
);

echo $request->getUrl(); // redirect to this url

echo "\n\nCard payment:\n";

$billingAddress = new \TrustPay\BillingAddress(
    'John Doe',
    'Fake street',
    'Fake city',
    'Fake ZIP',
    'Fake country'
);

$request = $trustPay->paymentWithCard(
    0.01,
    'variable symbol',
    'test@example.com',
    $billingAddress,
    'Payment description...'
);

echo $request->getUrl();