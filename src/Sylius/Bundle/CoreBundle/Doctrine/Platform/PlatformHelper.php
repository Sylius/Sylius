<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\Platform;

final class PlatformHelper
{
    private function __construct()
    {
    }

    /** Compatibility layer for DBAL 3.x (SqlitePlatform) and 4.x (SQLitePlatform). */
    public static function isSqlite(object $platform): bool
    {
        return str_contains(strtolower($platform::class), 'sqliteplatform');
    }
}
