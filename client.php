<?php

// Define credentials
$partnerId = 123456;
$secret = 'secret';

// Define entrypoint API URL
$apiUrl = 'http://dev.bridge.drhead.ru/php-api/entrypoint.php';
//$apiUrl = 'http://example.com/entrypoint.php';

// Prepare request data
$request = array(
    'partnerId' => $partnerId,
    'arrayOfNumbers' => array(1, 2, 3, 4),
    'arrayOfArrays' => array(array('foo' => 'foo'), array('bar' => 'bar')),
    'bool' => true,
    'num' => 123,
    'str' => 'Hello!'
);

// Encode request and generate signature
$requestBody = json_encode($request);
$requestSignature = sha1($secret . $requestBody);

// Prepare CURL
$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    "X-Api-Signature: {$requestSignature}"
));

// Send request
$response = curl_exec($curl);
$info = curl_getinfo($curl);

// Check status code of request
if ($info['http_code'] !== 200) {
    throw new Exception('Unknown error');
}

// Parse & check response
$data = json_decode($response);
if (!$data) {
    throw new Exception("Invalid response:\n$response");
}

// Check for errors in API response
if (array_key_exists('error', $data)) {
    throw new Exception($response['error']);
}

// We'd done
var_dump($data);