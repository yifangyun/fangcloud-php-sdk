<?php
session_start();
require_once('UserDB.php');
require_once('YfyClientFactory.php');

$username = $_SESSION['logged_in_username'];

$db = UserDB::getDB();
$client = YfyClientFactory::getClient();
$res = $client->oauth()->finishAuthorizationCodeFlow();
$saveData = [];
$saveData['access_token'] = $res['access_token'];
$saveData['refresh_token'] = $res['refresh_token'];

$db->saveUser($username, $saveData);
header('Location: http://' . $_SERVER['HTTP_HOST'] . '/home.php');