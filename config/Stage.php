<?php
/*
 * This file is part of the pofolio project.
 *
 * @copyright Copyright (c) 2023 Alexander Manhart IT
 * @authors Alexander Manhart
 *
 * Stage.php created on 07.05.23, 21:35.
 */

namespace pofolio\config;

enum Stage: string
{
    case develop = 'dev';
    case staging = 'stg';
    case production = 'prod';

    public static function fromString(string $name): Stage
    {
        return match ($name) {
            'develop' => self::develop,
            'staging' => self::staging,
            'production' => self::production,
        };
    }
}