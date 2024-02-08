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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class LocaleContext implements Context
{
    public function __construct(
        private LocaleConverterInterface $localeNameConverter,
        private RepositoryInterface $localeRepository,
        private SharedStorageInterface $sharedStorage,
    ) {
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
     * @Transform :localeNameInItsLocale
     */
    public function castToItsLocale(string $localeName): string
    {
        $localeCode = $this->localeNameConverter->convertNameToCode($localeName);

        return $this->localeNameConverter->convertCodeToName($localeCode, $localeCode);
    }

    /**
     * @Transform :localeNameInCurrentLocale
     */
    public function castToCurrentLocale(string $localeName): string
    {
        if ($this->sharedStorage->has('current_locale_code')) {
            return $this->localeNameConverter->convertCodeToName(
                $this->localeNameConverter->convertNameToCode($localeName),
                $this->sharedStorage->get('current_locale_code'),
            );
        }

        return $localeName;
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
            sprintf('Cannot find "%s" locale.', $name),
        );

        return $locale;
    }
}
