<?php
require_once 'php/config.php';
/**
 * Instruction:
 *
 * 1. Replace the APIKEY with your API Key.
 * 2. Replace the COLLECTION with your Collection ID.
 * 3. Replace the X_SIGNATURE with your X Signature Key
 * 4. Change $is_sandbox = false to $is_sandbox = true for sandbox
 * 5. Replace the http://www.google.com with the full path to the site.
 * 6. Replace the http://www.google.com/success.html with the full path to your success page. *The URL can be overridden later
 * 7. OPTIONAL: Set $amount value.
 * 8. OPTIONAL: Set $fallbackurl if the user are failed to be redirected to the Billplz Payment Page.
 *
 */
$api_key = '79381b68-4676-440f-9613-05e30b109d7c';
$collection_id = 'u121pgx5';
$x_signature = 'd2a0cafef9b99b1d7c3c6bf962a64fa7fb6e1a35ec756c694ecb5add868a6f5f8cdec020e1c5f42f94ef4d788c8089941a664197749687384afff99574a099d7';
$is_sandbox = true;

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$websiteurl = BASE_URL;
$successpath = $websiteurl . '/payment_success.php';
$amount = ''; //Example (RM13.50): $amount = '1350';
$fallbackurl = $websiteurl . '/payment_failed.php'; //Example: $fallbackurl = 'http://www.google.com/pay.php';
$description = 'PAYMENT DESCRIPTION';
$reference_1_label = '';
$reference_2_label = '';

$debug = true;
