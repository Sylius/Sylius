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

use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;
use Webmozart\Assert\Assert;

final class LocaleConverter implements LocaleConverterInterface
{
    public function convertNameToCode(string $name, ?string $locale = null): string
    {
        $names = Locales::getNames($locale ?? 'en');
        $code = array_search($name, $names, true);

        Assert::string($code, sprintf('Cannot find code for "%s" locale name', $name));

        return $code;
    }

    public function convertCodeToName(string $code, ?string $locale = null): string
    {
        try {
            return Locales::getName($code, $locale ?? 'en');
        } catch (MissingResourceException $e) {
            throw new \InvalidArgumentException(sprintf('Cannot find name for "%s" locale code', $code), 0, $e);
        }
    }
}
