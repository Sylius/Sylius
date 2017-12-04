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

use Symfony\Component\Intl\Intl;
use Webmozart\Assert\Assert;

final class LocaleConverter implements LocaleConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertNameToCode(string $name, ?string $locale = null): string
    {
        $names = Intl::getLocaleBundle()->getLocaleNames($locale ?? 'en');
        $code = array_search($name, $names, true);

        Assert::string($code, sprintf('Cannot find code for "%s" locale name', $name));

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCodeToName(string $code, ?string $locale = null): string
    {
        $name = Intl::getLocaleBundle()->getLocaleName($code, $locale ?? 'en');

        Assert::string($name, sprintf('Cannot find name for "%s" locale code', $code));

        return $name;
    }
}
