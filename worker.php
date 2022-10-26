<?php

use Predis\Client;
use Ully\Queue\Expiration;

require 'vendor/autoload.php';

const MAX_ACTIVE_USERS_COUNT = 10;

$client = new Client();

function getActiveUsersCount(Client $client): int
{
    $curTime = time();
    return $client->zcount(
        'active_users',
        $curTime,
        $curTime - Expiration::ACTIVE_USER_LIMIT
    );
}

while (true) {
    if (getActiveUsersCount($client) > MAX_ACTIVE_USERS_COUNT) {
        continue;
    }
    $user = $client->lpop('waiting');
    if (!$timeRemainder = $client->ttl($user)) {
        continue;
    }
    $client->expire($user, Expiration::ACTIVE_USER_LIMIT - $timeRemainder);
    $lastTimeVisited = time() - $timeRemainder;
    $client->zadd('active_users', [$user => $lastTimeVisited]);
}