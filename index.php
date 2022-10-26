<?php

require 'vendor/autoload.php';

const VISITER_INACTIVITY_LIMIT = 15;
const WAITING_EXPIRE_TIME = 5;


$client = new Predis\Client();
$sessionId = $argv[1];
$userRequestTime = $_SERVER['REQUEST_TIME'];


if ($sessionId && $user = $client->get($sessionId)) {
    $lastVisitTime = $client->zscore('active_users', $user);
    if ($lastVisitTime > $userRequestTime - VISITER_INACTIVITY_LIMIT){
        $client->zadd('active_users', [$user => VISITER_INACTIVITY_LIMIT]);
        $client->expire($user, VISITER_INACTIVITY_LIMIT);
    }
    die();
}
if ($sessionId === null) {
    $sessionId = uniqid();
    $client->rpush('waiting', [$sessionId]);
}

$client->set($sessionId, 1, WAITING_EXPIRE_TIME);




