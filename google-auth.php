<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setApplicationName('Web Pengingat Deadline Akademik');

// ✅ SCOPE WAJIB
$client->setScopes([
    Google_Service_Calendar::CALENDAR
]);

$client->setAuthConfig(__DIR__ . '/credentials.json');

// ✅ WAJIB ADA
$client->setRedirectUri('http://localhost/schedule/oauth_google.php');

// ✅ WAJIB UNTUK SIMPAN TOKEN
$client->setAccessType('offline');

// ✅ WAJIB AGAR REFRESH TOKEN SELALU ADA
$client->setPrompt('consent select_account');

if (!isset($_GET['code'])) {

    // STEP 1: Redirect ke Google Login
    $authUrl = $client->createAuthUrl();
    header("Location: $authUrl");
    exit;

} else {

    // STEP 2: Google redirect balik
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        echo "❌ OAuth gagal: " . $token['error_description'];
        exit;
    }

    // 🔥 SIMPAN TOKEN
    file_put_contents(__DIR__ . '/token.json', json_encode($token));

    echo "✅ OAuth BERHASIL.<br>";
    echo "token.json sudah dibuat.<br>";
    echo "Silakan tutup halaman ini.";
}
