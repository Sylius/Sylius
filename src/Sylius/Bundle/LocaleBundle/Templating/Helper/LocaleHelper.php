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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Converter\LocaleConverter;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

trigger_deprecation(
    'sylius/locale-bundle',
    '1.14',
    'The "%s" class is deprecated, use "%s" instead.',
    LocaleHelper::class,
    LocaleConverter::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Locale\Converter\LocaleConverter} instead. */
final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    public function __construct(
        private LocaleConverterInterface $localeConverter,
        private ?LocaleContextInterface $localeContext = null,
    ) {
        if (null === $localeContext) {
            trigger_deprecation(
                'sylius/locale-bundle',
                '1.4',
                'Not passing a $localeContext explicitly as the second argument is deprecated and will be prohibited in Sylius 2.0',
            );
        }
    }

    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        try {
            return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (\InvalidArgumentException) {
            return $code;
        }
    }

    public function getName(): string
    {
        return 'sylius_locale';
    }

    private function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        if (null === $this->localeContext) {
            return null;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            return null;
        }
    }
}
