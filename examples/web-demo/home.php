<?php
require_once('UserDB.php');
require_once('YfyClientFactory.php');


session_start();

$username = $_SESSION['logged_in_username'];

echo '<html>';
echo '<head><title>Home - Web demo</title></head>';
echo '<body>';
echo "<h2>User: $username</h2>";

$db = UserDB::getDB();
$client = YfyClientFactory::getClient();

if ($db->getUser($username)) {
    $userData =  $db->getUser($username);
    $client->setAccessToken($userData['access_token']);
    $client->setRefreshToken($userData['refresh_token']);
    $userInfo = $client->users()->getSelf();

    echo '<p>Linked to your Fangcloud account with access token: ' . $userData['access_token'] . '</p>';
    echo '<p>your Fangcloud account name: ' . $userInfo['name'] . '</p>';
    echo '<p>your Fangcloud account id: ' . $userInfo['id'] . '</p>';
    echo '<p>your Fangcloud account email: ' . $userInfo['email'] . '</p>';
}
else {
    $authorizationUrl =  $client->oauth()->getAuthorizationUrl();
    echo '<a href="' . $authorizationUrl . '">Link to your Fangcloud account</a>';
}