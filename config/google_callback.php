<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['oauth_user_id'])) {
    die('❌ OAuth user tidak ditemukan');
}

$user_id = (int) $_SESSION['oauth_user_id'];

$client = new Google_Client();
$client->setApplicationName('Web Pengingat Deadline Akademik');
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');
$client->setPrompt('consent select_account');
$client->setRedirectUri('http://localhost/schedule/config/google_callback.php');

if (!isset($_GET['code'])) {
    die('❌ Code OAuth tidak ditemukan');
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die('❌ OAuth gagal: ' . $token['error_description']);
}

/* ================= SIMPAN TOKEN PER USER ================= */
$tokenDir = __DIR__ . '/../tokens';
if (!is_dir($tokenDir)) {
    mkdir($tokenDir, 0777, true);
}

$token['created'] = time();
$tokenPath = $tokenDir . '/token_user_' . $user_id . '.json';
file_put_contents($tokenPath, json_encode($token));

/* ================= CLEAN SESSION ================= */
unset($_SESSION['oauth_user_id']);

echo "
    <h3>✅ Google Calendar berhasil terhubung untuk user ini.</h3>
    <a href='/schedule/dashboard/index.php'>⬅️ Kembali ke Dashboard</a>
";
