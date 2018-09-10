<?php
// # VoidAuthorization
// This sample code demonstrates how you can 
// void an authorized payment.
// API used: /v1/payments/authorization/<{authorizationid}>/void"


// Replace $authorizationId with any static Id you might already have. 
$authorizationId = "<your authorization id here>";

use PayPal\Api\Authorization;

// ### VoidAuthorization
// You can void a previously authorized payment
// by invoking the $authorization->void method
// with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
try {

    // Lookup the authorization
    $authorization = Authorization::get($authorizationId, $apiContext);

    // Void the authorization
    $voidedAuth = $authorization->void($apiContext);
} catch (Exception $ex) {
    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
    ResultPrinter::printError("Void Authorization", "Authorization", null, null, $ex);
    exit(1);
}

// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
 ResultPrinter::printResult("Void Authorization", "Authorization", $voidedAuth->getId(), null, $voidedAuth);

return $voidedAuth;
