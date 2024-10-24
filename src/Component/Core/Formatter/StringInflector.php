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

namespace Sylius\Component\Core\Formatter;

use Behat\Transliterator\Transliterator;
use function Symfony\Component\String\u;

final class StringInflector
{
    public static function codeToName(string $value): string
    {
        return ucfirst(str_replace('_', ' ', $value));
    }

    public static function nameToCode(string $value): string
    {
        return str_replace([' ', '-', '\''], '_', $value);
    }

    public static function nameToSlug(string $value): string
    {
        return str_replace(['_'], '-', self::nameToLowercaseCode(Transliterator::transliterate($value)));
    }

    public static function nameToLowercaseCode(string $value): string
    {
        return strtolower(self::nameToCode($value));
    }

    public static function nameToUppercaseCode(string $value): string
    {
        return strtoupper(self::nameToCode($value));
    }

    public static function nameToCamelCase(string $value): string
    {
        return (string) u($value)->camel();
    }

    private function __construct()
    {
    }
}
