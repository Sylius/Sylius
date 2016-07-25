<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Converter;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface LocaleConverterInterface
{
    /**
     * @param string $name
     * @param string $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertNameToCode($name, $locale = 'en');

    /**
     * @param string $code
     * @param string $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertCodeToName($code, $locale = 'en');
}
