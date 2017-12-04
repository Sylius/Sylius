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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

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
        return $this->localeNameConverter->convertNameToCode($localeName);
    }

    /**
     * @Transform :localeName
     */
    public function castToLocaleName($localeCode)
    {
        return $this->localeNameConverter->convertCodeToName($localeCode);
    }
}
