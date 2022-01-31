<?php
require __DIR__ . '/vendor/autoload.php';

use Twilio\TwiML\VoiceResponse;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function get_voice_response($phone) {
    $response = new VoiceResponse();
    if ($phone == $_ENV['TWILIO_CALLER_ID']) {
        # Receiving an incoming call to the browser from an external phone
        $response = new VoiceResponse();
        $dial = $response->dial('');
        $dial->client($_SESSION['identity']);
    } else if (!empty($phone) && strlen($phone) > 0) {
        $number = htmlspecialchars($phone);
        $dial = $response->dial('', ['callerId' => $_ENV['TWILIO_CALLER_ID']]);
        
        // wrap the phone number or client name in the appropriate TwiML verb
        // by checking if the number given has only digits and format symbols
        if (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
            $dial->number($number);
        } else {
            $dial->client($number);
        }
    } else {
        $response->say("Thanks for calling!");
    }
    return (string)$response;
}


// get the phone number from the page request parameters, if given
header('Content-Type: text/xml');
$phone = $_REQUEST['To'] ?? null;

echo get_voice_response($_REQUEST['To'] ?? null);
