<?php
session_start();
$username = $_POST['username'];
$_SESSION['logged_in_username'] = $username;

header('Location: http://' . $_SERVER['HTTP_HOST'] . '/home.php');
