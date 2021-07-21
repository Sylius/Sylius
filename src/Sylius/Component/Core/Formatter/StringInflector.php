<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Formatter;

use Behat\Transliterator\Transliterator;

final class StringInflector
{
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
        return lcfirst(str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9\x7f-\xff]++/', ' ', $value))));
    }

    private function __construct()
    {
    }
}
