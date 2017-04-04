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

use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Intl\Intl;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LocaleConverter implements LocaleConverterInterface
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function convertNameToCode($name, $locale = null)
    {
        if (null === $locale) {
            $locale = $this->localeContext->getLocaleCode();
        }

        $names = Intl::getLocaleBundle()->getLocaleNames($locale);
        $code = array_search($name, $names, true);

        Assert::string($code, sprintf('Cannot find code for "%s" locale name', $name));

        return $code;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCodeToName($code, $locale = null)
    {
        if (null === $locale) {
            $locale = $this->localeContext->getLocaleCode();
        }

        $name = Intl::getLocaleBundle()->getLocaleName($code, $locale);

        Assert::notNull($name, sprintf('Cannot find name for "%s" locale code', $code));

        return $name;
    }
}
