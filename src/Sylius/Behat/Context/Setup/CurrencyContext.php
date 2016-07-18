<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CurrencyContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @var ObjectManager
     */
    private $currencyManager;

    /**
     * @var ObjectManager
     */
    private $channelManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $currencyRepository
     * @param FactoryInterface $currencyFactory
     * @param ObjectManager $currencyManager
     * @param ObjectManager $channelManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $currencyRepository,
        FactoryInterface $currencyFactory,
        ObjectManager $currencyManager,
        ObjectManager $channelManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
        $this->currencyManager = $currencyManager;
        $this->channelManager = $channelManager;
    }

    /**
     * @Given default currency is :currencyCode
     */
    public function defaultCurrencyIs($currencyCode)
    {
        $currency = $this->createCurrency($currencyCode);
        $currency->setEnabled(true);

        $channel = $this->sharedStorage->get('channel');
        $channel->setDefaultCurrency($currency);

        $this->saveCurrency($currency);
    }

    /**
     * @Given the store has a base currency :currencyCode
     */
    public function theStoreHasBasedCurrency($currencyCode)
    {
        $currency = $this->createCurrency($currencyCode);
        $currency->setEnabled(true);

        $this->saveCurrency($currency);
    }

    /**
     * @Given the store has currency :currencyCode
     */
    public function theStoreHasCurrency($currencyCode)
    {
        $currency = $this->createCurrency($currencyCode);
        $currency->setEnabled(true);

        $this->saveCurrency($currency);
    }

    /**
     * @Given the store has currency :currencyCode, :secondCurrencyCode
     */
    public function theStoreHasCurrencyAnd($currencyCode, $secondCurrencyCode)
    {
        $this->saveCurrency($this->createCurrency($currencyCode));
        $this->saveCurrency($this->createCurrency($secondCurrencyCode));
    }

    /**
     * @Given the store has disabled currency :currencyCode
     */
    public function theStoreHasDisabledCurrency($currencyCode)
    {
        $currency = $this->createCurrency($currencyCode);
        $currency->setEnabled(false);

        $this->saveCurrency($currency);
    }

    /**
     * @Given the store has currency :currencyCode with exchange rate :exchangeRate
     */
    public function theStoreHasCurrencyWithExchangeRate($currencyCode, $exchangeRate)
    {
        $currency = $this->createCurrency($currencyCode, $exchangeRate);
        $currency->setEnabled(true);

        $this->saveCurrency($currency);
    }

    /**
     * @Given /^(that channel) allows to shop using "([^"]+)" and "([^"]+)" currencies$/
     */
    public function thatChannelAllowsToShopUsingAndCurrencies(ChannelInterface $channel, $firstCurrencyCode, $secondCurrencyCode)
    {
        $channel->setCurrencies(new ArrayCollection([
            $this->provideCurrency($firstCurrencyCode),
            $this->provideCurrency($secondCurrencyCode),
        ]));

        $this->channelManager->flush();
    }

    /**
     * @Given /^(it) uses the "([^"]+)" currency by default$/
     */
    public function itUsesTheCurrencyByDefault(ChannelInterface $channel, $currencyCode)
    {
        $currency = $this->provideCurrency($currencyCode);
        $currency->setExchangeRate(1.0);

        $this->currencyManager->flush();

        $channel->setDefaultCurrency($currency);

        $this->channelManager->flush();
    }

    /**
     * @param CurrencyInterface $currency
     */
    private function saveCurrency(CurrencyInterface $currency)
    {
        $this->sharedStorage->set('currency', $currency);
        $this->currencyRepository->add($currency);
    }

    /**
     * @param $currencyCode
     * @param float $exchangeRate
     *
     * @return CurrencyInterface
     */
    private function createCurrency($currencyCode, $exchangeRate = 1.0)
    {
        /** @var CurrencyInterface $currency */
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($currencyCode);
        $currency->setExchangeRate($exchangeRate);

        return $currency;
    }

    /**
     * @param string $currencyCode
     *
     * @return CurrencyInterface
     */
    private function provideCurrency($currencyCode)
    {
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);
        if (null === $currency) {
            /** @var CurrencyInterface $currency */
            $currency = $this->createCurrency($currencyCode, mt_rand(0, 200) / 100);

            $this->currencyRepository->add($currency);
        }

        return $currency;
    }
}
