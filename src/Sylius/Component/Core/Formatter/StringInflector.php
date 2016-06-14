<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Formatter;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class StringInflector
{
    /**
     * @param string $value
     *
     * @return string
     */
    public static function nameToCode($value)
    {
        return str_replace(' ', '_', $value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function nameToLowercaseCode($value)
    {
        return strtolower(self::nameToCode($value));
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function nameToUpercaseCode($value)
    {
        return strtoupper(self::nameToCode($value));
    }
}
