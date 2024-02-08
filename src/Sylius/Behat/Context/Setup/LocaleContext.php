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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private LocaleConverterInterface $localeConverter,
        private FactoryInterface $localeFactory,
        private RepositoryInterface $localeRepository,
        private ObjectManager $localeManager,
        private ObjectManager $channelManager,
    ) {
    }

    /**
     * @Given the store has locale :localeCode
     * @Given the store is( also) available in :localeCode
     * @Given the locale :localeCode is enabled
     */
    public function theStoreHasLocale($localeCode)
    {
        $locale = $this->provideLocale($localeCode);

        $this->saveLocale($locale);
    }

    /**
     * @Given the store has many locales
     */
    public function theStoreHasManyLocales(): void
    {
        $this->theStoreHasLocale('en_US');
        $this->theStoreHasLocale('fr_FR');
        $this->theStoreHasLocale('de_DE');
        $this->theStoreHasLocale('es_ES');
        $this->theStoreHasLocale('pl_PL');
        $this->theStoreHasLocale('pt_PT');
        $this->theStoreHasLocale('uk_UA');
        $this->theStoreHasLocale('ja_JP');
        $this->theStoreHasLocale('zh_CN');
        $this->theStoreHasLocale('bg_BG');
        $this->theStoreHasLocale('da_DK');
    }

    /**
     * @Given the locale :localeCode does not exist in the store
     */
    public function theStoreDoesNotHaveLocale($localeCode)
    {
        /** @var LocaleInterface $locale */
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);
        if (null !== $locale) {
            $this->localeRepository->remove($locale);
        }
    }

    /**
     * @Given /^(that channel) allows to shop using the "([^"]+)" locale$/
     * @Given /^(that channel) allows to shop using "([^"]+)" and "([^"]+)" locales$/
     * @Given /^(that channel) allows to shop using "([^"]+)", "([^"]+)" and "([^"]+)" locales$/
     * @Given /^(this channel) allows to shop using the "([^"]+)" locale$/
     * @Given /^(this channel) allows to shop using "([^"]+)" and "([^"]+)" locales$/
     */
    public function thatChannelAllowsToShopUsingAndLocales(ChannelInterface $channel, ...$localesNames)
    {
        foreach ($channel->getLocales() as $locale) {
            $channel->removeLocale($locale);
        }

        foreach ($localesNames as $localeName) {
            $channel->addLocale($this->provideLocale($this->localeConverter->convertNameToCode($localeName)));
        }

        $this->channelManager->flush();
    }

    /**
     * @Given /^(it) uses the "([^"]+)" locale by default$/
     * @Given /^(this channel) uses the "([^"]+)" locale as default$/
     */
    public function itUsesTheLocaleByDefault(ChannelInterface $channel, string $localeName): void
    {
        $locale = $this->provideLocale($this->localeConverter->convertNameToCode($localeName));

        $this->localeManager->flush();

        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);

        $this->channelManager->flush();
    }

    /**
     * @param string $localeCode
     *
     * @return LocaleInterface
     */
    private function createLocale($localeCode)
    {
        /** @var LocaleInterface $locale */
        $locale = $this->localeFactory->createNew();
        $locale->setCode($localeCode);

        return $locale;
    }

    /**
     * @param string $localeCode
     *
     * @return LocaleInterface
     */
    private function provideLocale($localeCode)
    {
        $locale = $this->localeRepository->findOneBy(['code' => $localeCode]);
        if (null === $locale) {
            /** @var LocaleInterface $locale */
            $locale = $this->createLocale($localeCode);

            $this->localeRepository->add($locale);
        }

        return $locale;
    }

    private function saveLocale(LocaleInterface $locale)
    {
        $this->sharedStorage->set('locale', $locale);
        $this->localeRepository->add($locale);
    }
}
