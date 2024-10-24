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

namespace Sylius\Component\Locale\Converter;

interface LocaleConverterInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function convertNameToCode(string $name, ?string $locale = null): string;

    /**
     * @throws \InvalidArgumentException
     */
    public function convertCodeToName(string $code, ?string $locale = null): string;
}
