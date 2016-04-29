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
use Sylius\Component\Locale\Converter\LocaleNameConverterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class LocaleContext implements Context
{
    /**
     * @var LocaleNameConverterInterface
     */
    private $localeNameConverter;

    /**
     * @param LocaleNameConverterInterface $localeNameConverter
     */
    public function __construct(LocaleNameConverterInterface $localeNameConverter)
    {
        $this->localeNameConverter = $localeNameConverter;
    }

    /**
     * @Transform :language
     * @Transform :localeCode
     */
    public function getLocaleCode($languageName)
    {
        return $this->localeNameConverter->convertToCode($languageName);
    }
}
