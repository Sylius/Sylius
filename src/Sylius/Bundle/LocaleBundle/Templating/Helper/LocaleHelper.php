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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    /**
     * @var LocaleConverterInterface
     */
    private $localeConverter;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param LocaleConverterInterface $localeConverter
     * @param string $defaultLocale
     */
    public function __construct(LocaleConverterInterface $localeConverter, string $defaultLocale)
    {
        $this->localeConverter = $localeConverter;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCodeToName(string $localeCode, ?string $locale = null): ?string
    {
        if (null === $locale) {
            $locale = $this->defaultLocale;
        }

        return $this->localeConverter->convertCodeToName($localeCode, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_locale';
    }
}
