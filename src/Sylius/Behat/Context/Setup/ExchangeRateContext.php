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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ExchangeRateContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $exchangeRateFactory;

    /**
     * @var RepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $exchangeRateFactory
     * @param RepositoryInterface $exchangeRateRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $exchangeRateFactory,
        RepositoryInterface $exchangeRateRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->exchangeRateFactory = $exchangeRateFactory;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * @Given /^the store (?:|also )has an exchange rate ([0-9\.]+) with base (currency "[^"]+") and counter (currency "[^"]+")$/
     */
    public function thereIsAnExchangeRateWithBaseCurrencyAndCounterCurrency(
        $ratio,
        CurrencyInterface $baseCurrency,
        CurrencyInterface $counterCurrency
    ) {
        $exchangeRate = $this->createExchangeRate($baseCurrency, $counterCurrency, $ratio);

        $this->saveExchangeRate($exchangeRate);
    }

    /**
     * @param CurrencyInterface $baseCurrency
     * @param CurrencyInterface $counterCurrency
     * @param float $ratio
     *
     * @return ExchangeRateInterface
     */
    private function createExchangeRate(CurrencyInterface $baseCurrency, CurrencyInterface $counterCurrency, $ratio = 1.00)
    {
        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $this->exchangeRateFactory->createNew();
        $exchangeRate->setBaseCurrency($baseCurrency);
        $exchangeRate->setCounterCurrency($counterCurrency);
        $exchangeRate->setRatio((float) $ratio);

        return $exchangeRate;
    }

    /**
     * @param ExchangeRateInterface $exchangeRate
     */
    private function saveExchangeRate(ExchangeRateInterface $exchangeRate)
    {
        $this->exchangeRateRepository->add($exchangeRate);
        $this->sharedStorage->set('exchange_rate', $exchangeRate);
    }
}
