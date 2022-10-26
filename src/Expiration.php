<?php

namespace Ully\Queue;

class Expiration
{
    const ACTIVE_USER_LIMIT = 5 * 60;
    const WAITING_USER_LIMIT = 2 * 60;


    public static function getByUserType(int $userType): int
    {
        return match ($userType) {
            UserType::ACTIVE => self::ACTIVE_USER_LIMIT,
            default => self::WAITING_USER_LIMIT
        };
    }
}