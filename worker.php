<?php


use Predis\Client;

require 'vendor/autoload.php';

const VISITER_INACTIVITY_LIMIT = 15;
const WAITING_EXPIRE_TIME = 5;
const MAX_ACTIVE_USERS_COUNT = 10;

$client = new Client();

function getActiveUsersCount(Client $client): int
{
    $curTime = time();
    return $client->zcount(
        'active_users',
        $curTime,
        $curTime - VISITER_INACTIVITY_LIMIT
    );
}

while (true) {
    if (getActiveUsersCount($client) < MAX_ACTIVE_USERS_COUNT) {
        continue;
    }
    $user = $client->lpop('waiting');
    if (!$timeRemainder = $client->ttl($user)) {
        continue;
    }
    $client->expire($user, VISITER_INACTIVITY_LIMIT - $timeRemainder);
    $lastTimeVisited = time() - $timeRemainder;
    $client->zadd('active_users', [$user => $lastTimeVisited]);
}