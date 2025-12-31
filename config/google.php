<?php
use Google\Client;

function getGoogleClient(int $user_id)
{
    $client = new Google_Client();
    $client->setApplicationName('Web Pengingat Deadline Akademik');
    $client->setAuthConfig(__DIR__ . '/credentials.json');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAccessType('offline');
    $client->setPrompt('consent select_account');
    $client->setRedirectUri('http://localhost/schedule/config/google_callback.php');

    $tokenPath = __DIR__ . '/../tokens/token_user_' . $user_id . '.json';

    if (!file_exists($tokenPath)) {
        return null;
    }

    $token = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($token);

    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $client->getRefreshToken()
            );
            $newToken['created'] = time();
            file_put_contents($tokenPath, json_encode($newToken));
        } else {
            return null;
        }
    }

    return $client;
}

