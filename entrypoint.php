<?php

try {
    // Response object
    $response = array();

    // Check request method (POST only)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Request method {$_SERVER['REQUEST_METHOD']} is not allowed");
    }

    // Get request body
    $rawPostData = file_get_contents('php://input');

    // API Signature (sha1 of json body)
    $xApiSignature = $_SERVER['HTTP_X_API_SIGNATURE'];

    // Fallback checks for older php version
    if (empty($rawPostData)) {
        $rawPostData = $HTTP_RAW_POST_DATA;
    }

    if (!$rawPostData) {
        throw new Exception('Empty request');
    }

    $input = json_decode($rawPostData, true);

    // Check the credentials
    $partnerId = $input['partner_id'];
    $secret = 'secret'; // Secret should be unique by partner

    if ($xApiSignature !== sha1($secret . $rawPostData)) {
        throw new Exception('Invalid signature');
    }

    // do something with input...

    $response = $input; // assign $input to $response as an example

} catch (Exception $exception) { // Catch an exception and assign error to response
    $response = array(
        'error' => array(
            'message' => $exception->getMessage()
        )
    );
}

header('Content-Type: application/json');
if (empty($response)) {
    echo "{}"; // Empty response serialization fix
} else {
    echo json_encode($response);
}