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
 * @author Stefan Doorn <stefan@efectos.nl>
 */
final class FormAllowedCharacters
{
    /**
     * @param $value
     * @return string
     *
     * Transform values to avoid error below.
     *
     * The name "25% discount!" contains illegal characters. Names should start with a letter,
     * digit or underscore and only contain letters, digits, numbers, underscores ("_"),
     * hyphens ("-") and colons (":").
     */
    public static function nameToAllowedCharacters($value)
    {
        return self::startCharacter(self::consecutiveCharacters($value));
    }

    /**
     * @param $value
     * @return string
     */
    private static function startCharacter($value)
    {
        // If we match the first character for above error, we just return
        if (preg_match('/[a-zA-Z0-9_]/', substr($value, 0, 1)) === 1) {
            return $value;
        }

        // Else remove first character and try again
        return self::startCharacter(substr($value, 1));
    }

    /**
     * @param $value
     * @return mixed
     */
    private static function consecutiveCharacters($value)
    {
        return preg_replace('/[^:0-9a-zA-Z_-]/', '', $value);
    }

    private function __construct()
    {
    }
}
