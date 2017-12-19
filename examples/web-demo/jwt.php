<?php

use Fangcloud\Constant\YfyJwtSubType;

require_once('YfyClientFactory.php');

$client = YfyClientFactory::getClient();
$result = [];
$result['enterprise_token'] = $client->oauth()->getTokenByJwtFlow(YfyJwtSubType::ENTERPRISE, 12401, 'U7TejSsByn', 'private_key.pem');
$result['user_token'] = $client->oauth()->getTokenByJwtFlow(YfyJwtSubType::USER, 881525, 'U7TejSsByn', 'private_key.pem');

var_dump($result);