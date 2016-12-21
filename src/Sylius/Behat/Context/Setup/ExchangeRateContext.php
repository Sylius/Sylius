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
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
     * @var ExchangeRateRepositoryInterface
     */
    private $exchangeRateRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $exchangeRateFactory
     * @param ExchangeRateRepositoryInterface $exchangeRateRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $exchangeRateFactory,
        ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->exchangeRateFactory = $exchangeRateFactory;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * @Given the exchange rate of :sourceCurrency to :targetCurrency is :ratio
     */
    public function thereIsAnExchangeRateWithSourceCurrencyAndTargetCurrency(
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
        $ratio
    ) {
        $exchangeRate = $this->createExchangeRate($sourceCurrency, $targetCurrency, $ratio);

        $this->saveExchangeRate($exchangeRate);
    }

    /**
     * @param CurrencyInterface $sourceCurrency
     * @param CurrencyInterface $targetCurrency
     * @param float $ratio
     *
     * @return ExchangeRateInterface
     */
    private function createExchangeRate(CurrencyInterface $sourceCurrency, CurrencyInterface $targetCurrency, $ratio = 1.00)
    {
        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $this->exchangeRateFactory->createNew();
        $exchangeRate->setSourceCurrency($sourceCurrency);
        $exchangeRate->setTargetCurrency($targetCurrency);
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
