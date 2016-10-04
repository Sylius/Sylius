<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class LocaleContext implements Context
{
    /**
     * @var LocaleConverterInterface
     */
    private $localeNameConverter;

    /**
     * @param LocaleConverterInterface $localeNameConverter
     */
    public function __construct(LocaleConverterInterface $localeNameConverter)
    {
        $this->localeNameConverter = $localeNameConverter;
    }

    /**
     * @Transform :language
     * @Transform :localeCode
     * @Transform /^"([^"]+)" locale$/
     * @Transform /^in the "([^"]+)" locale$/
     */
    public function castToLocaleCode($localeName)
    {
        try {
            return $this->localeNameConverter->convertNameToCode($localeName);
        } catch (\InvalidArgumentException $exception) {
            return $localeName;
        }
    }

    /**
     * @Transform :localeName
     */
    public function castToLocaleName($localeCode)
    {
        try {
            return $this->localeNameConverter->convertCodeToName($localeCode);
        } catch (\InvalidArgumentException $exception) {
            return $localeCode;
        }
    }
}
