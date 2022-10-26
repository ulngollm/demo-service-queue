<?php

use Ully\Queue\Content;
use Ully\Queue\Expiration;
use Ully\Queue\UserType;

require 'vendor/autoload.php';

$client = new Predis\Client();
$sessionId = $argv[1];
$userRequestTime = $_SERVER['REQUEST_TIME'];


$userType = UserType::getUserType($sessionId, $client);

if ($userType === UserType::ACTIVE) {
    $lastVisitTime = $client->zscore('active_users', $sessionId);
    if ($lastVisitTime > $userRequestTime - Expiration::ACTIVE_USER_LIMIT) {
//            обновить время последнего визита
        $client->zadd('active_users', [$sessionId => Expiration::ACTIVE_USER_LIMIT]);
    }
}

if ($sessionId === null || empty($client->get($sessionId))) {
    $sessionId = uniqid();
    $client->set($sessionId, $userType);
    $client->rpush('waiting', [$sessionId]);
}

$client->expire($sessionId, Expiration::getByUserType($userType));

echo "$sessionId\n";
echo Content::getByUserType($userType) . PHP_EOL;




