<?php

namespace Ully\Queue;
use Predis\Client;

class UserType
{
    const WAITING = 2;
    const ACTIVE = 1;

    public static function getUserType(?string $sessionId, Client $client): int
    {
        if (!$sessionId) {
            return self::WAITING;
        }
        return $client->get($sessionId) ?? self::WAITING;

    }
}