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

use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LocaleNameConverter implements LocaleNameConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertToCode($name, $locale = 'en')
    {
        $localeNames = Intl::getLocaleBundle()->getLocaleNames($locale);
        $localeCode = array_search($name, $localeNames, true);

        if (false === $localeCode) {
            throw new \InvalidArgumentException(
                sprintf('Cannot find code for %s locale', $name)
            );
        }

        return $localeCode;
    }
}
