<?php

use Predis\Client;
use Ully\Queue\Expiration;
use Ully\Queue\UserType;

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
    $nextUser = $client->lpop('waiting');
    if ($nextUser === null) {
        continue;
    }
    $timeRemainder = $client->ttl($nextUser);
    if ($timeRemainder <= 0) {
        continue;
    }

    $client->set($nextUser, UserType::ACTIVE);
    $client->expire($nextUser, Expiration::ACTIVE_USER_LIMIT - $timeRemainder);

    $lastTimeVisited = time() - $timeRemainder;
    $client->zadd('active_users', [$nextUser => $lastTimeVisited]);
    echo "Add $nextUser";
}