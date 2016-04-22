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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;
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
     * @var CurrencyNameConverterInterface
     */
    private $currencyNameConverter;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $currencyRepository
     * @param FactoryInterface $currencyFactory
     * @param CurrencyNameConverterInterface $currencyNameConverter
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $currencyRepository,
        FactoryInterface $currencyFactory,
        CurrencyNameConverterInterface $currencyNameConverter
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
        $this->currencyNameConverter = $currencyNameConverter;
    }

    /**
     * @Given default currency is :currencyCode
     */
    public function defaultCurrencyIs($currencyCode)
    {
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($currencyCode);
        $currency->setExchangeRate(1.0);
        $channel = $this->sharedStorage->get('channel');
        $channel->setDefaultCurrency($currency);

        $this->currencyRepository->add($currency);
    }

    /**
     * @Given the store has currency :currencyName
     */
    public function theStoreHasCurrency($currencyName)
    {
        $this->createCurrency($currencyName);
    }

    /**
     * @Given the store has disabled currency :currencyName
     */
    public function theStoreHasDisabledCurrency($currencyName)
    {
        $this->createCurrency($currencyName, false);
    }

    /**
     * @Given the store has currency :currencyName with exchange rate :exchangeRate
     */
    public function theStoreHasCurrencyWithExchangeRate($currencyName, $exchangeRate)
    {
        $this->createCurrency($currencyName, true, $exchangeRate);
    }

    /**
     * @param string $currencyName
     * @param bool $enabled
     * @param float $exchangeRate
     */
    private function createCurrency($currencyName, $enabled = true, $exchangeRate = 1.0)
    {
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($this->currencyNameConverter->convertToCode($currencyName));
        $currency->setExchangeRate($exchangeRate);
        if (!$enabled) {
            $currency->disable();
        }

        $this->sharedStorage->set('currency', $currency);
        $this->currencyRepository->add($currency);
    }
}
