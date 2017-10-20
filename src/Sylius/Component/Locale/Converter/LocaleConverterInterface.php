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

namespace Sylius\Component\Locale\Converter;

interface LocaleConverterInterface
{
    /**
     * @param string $name
     * @param string|null $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertNameToCode(string $name, ?string $locale = null): string;

    /**
     * @param string $code
     * @param string|null $locale
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function convertCodeToName(string $code, ?string $locale = null): string;
}
