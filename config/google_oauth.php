<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    die('❌ User belum login');
}

$client = new Google_Client();
$client->setApplicationName('Web Pengingat Deadline Akademik');
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setAuthConfig(__DIR__ . '/credentials.json');
$client->setAccessType('offline');
$client->setPrompt('consent select_account');
$client->setRedirectUri('http://localhost/schedule/config/google_callback.php');

/* 🔑 SIMPAN USER_ID UNTUK CALLBACK */
$_SESSION['oauth_user_id'] = $_SESSION['user_id'];

header('Location: ' . $client->createAuthUrl());
exit;
