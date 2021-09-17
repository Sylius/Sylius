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
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    private LocaleConverterInterface $localeNameConverter;

    private RepositoryInterface $localeRepository;

    public function __construct(LocaleConverterInterface $localeNameConverter, RepositoryInterface $localeRepository)
    {
        $this->localeNameConverter = $localeNameConverter;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @Transform :language
     * @Transform :localeCode
     * @Transform /^"([^"]+)" locale$/
     * @Transform /^in the "([^"]+)" locale$/
     */
    public function castToLocaleCode(string $localeName): string
    {
        return $this->localeNameConverter->convertNameToCode($localeName);
    }

    /**
     * @Transform :localeName
     */
    public function castToLocaleName(string $localeCode): string
    {
        return $this->localeNameConverter->convertCodeToName($localeCode);
    }

    /**
     * @Transform :locale
     */
    public function getLocaleByName(string $name): LocaleInterface
    {
        $locale = $this->localeRepository->findOneBy(['code' => $this->localeNameConverter->convertNameToCode($name)]);

        Assert::isInstanceOf(
            $locale,
            LocaleInterface::class,
            sprintf('Cannot find "%s" locale.', $name)
        );

        return $locale;
    }
}
