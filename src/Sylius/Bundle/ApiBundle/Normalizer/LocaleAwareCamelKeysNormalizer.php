<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Normalizer;

use FOS\RestBundle\Normalizer\CamelKeysNormalizer;

/**
 * Normalizes the array by changing its keys from underscore to camel case, but does not perform this change if the key is an locale code.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class LocaleAwareCamelKeysNormalizer extends CamelKeysNormalizer
{
    /**
     * @param string $string
     *
     * @return string
     */
    protected function normalizeString($string)
    {
        if (false === strpos($string, '_')) {
            return $string;
        }

        // Custom logic to skip locale codes (e.g. nl_NL)
        if (1 === preg_match('/^[a-z]{2}_[A-Z]{2}$/', $string)) {
            return $string;
        }

        return preg_replace_callback('/_([a-zA-Z0-9])/', function ($matches) {
            return strtoupper($matches[1]);
        }, $string);
    }
}
