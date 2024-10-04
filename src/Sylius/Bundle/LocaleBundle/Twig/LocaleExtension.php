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

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class LocaleExtension extends AbstractExtension
{
    public function __construct(
        private LocaleConverterInterface|LocaleHelperInterface $localeHelper,
        private ?LocaleContextInterface $localeContext = null,
    ) {
        if ($this->localeHelper instanceof LocaleHelperInterface) {
            trigger_deprecation(
                'sylius/locale-bundle',
                '1.14',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                LocaleHelperInterface::class,
                self::class,
                LocaleConverterInterface::class,
            );

            trigger_deprecation(
                'sylius/locale-bundle',
                '1.14',
                'The argument name $localeHelper is deprecated and will be renamed to $localeConverter in Sylius 2.0.',
            );
        }

        if ($this->localeContext === null) {
            trigger_deprecation(
                'sylius/locale-bundle',
                '1.14',
                'Not passing a $localeContext to %s constructor as a second argument is deprecated and will be prohibited in Sylius 2.0.',
                self::class,
            );
        }
    }

    public function getFilters(): array
    {
        if (
            $this->localeHelper instanceof LocaleConverterInterface &&
            $this->localeContext instanceof LocaleContextInterface
        ) {
            return [
                new TwigFilter('sylius_locale_name', [$this, 'convertCodeToName']),
                new TwigFilter('sylius_locale_country', [$this, 'getCountryCode']),
            ];
        }

        return [
            new TwigFilter('sylius_locale_name', [$this->localeHelper, 'convertCodeToName']),
            new TwigFilter('sylius_locale_country', [$this, 'getCountryCode']),
        ];
    }

    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        try {
            return $this->localeHelper->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (\InvalidArgumentException) {
            return $code;
        }
    }

    public function getLocaleCode(?string $localeCode): ?string
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

    public function getCountryCode(string $locale): ?string
    {
        return \Locale::getRegion($locale);
    }
}
