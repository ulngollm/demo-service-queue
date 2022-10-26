<?php

namespace Ully\Queue;

class Content
{
    const DENIED = 'Ожидайте';
    const ALLOWED = 'Вы на сайте';

    public static function getByUserType(int $userType): string
    {
        return match ($userType) {
            UserType::ACTIVE => self::ALLOWED,
            default => self::DENIED
        };
    }
}