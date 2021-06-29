<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/randos.php';

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function get_access_token($identity) {
    $access_token = new AccessToken(
        $_ENV['TWILIO_ACCOUNT_SID'],
        $_ENV['API_KEY'],
        $_ENV['API_SECRET'],
        3600,
        $identity
    );
    
    // Create Voice grant
    $voiceGrant = new VoiceGrant();
    $voiceGrant->setOutgoingApplicationSid($_ENV['TWILIO_TWIML_APP_SID']);
    
    // Optional: add to allow incoming calls
    $voiceGrant->setIncomingAllow(true);
    
    // Add grant to token
    $access_token->addGrant($voiceGrant);
    
    // render token to string
    $token = $access_token->toJWT();

    return json_encode(array(
        'identity' => $identity,
        'token' => $token,
    ));
}

// choose a random username for the connecting user
$identity = randomUsername();
if(!session_id()) session_start();
$_SESSION['identity'] = $identity;

// return serialized token and the user's randomly generated ID
header('Content-Type: application/json');
echo get_access_token($_SESSION['identity']);
