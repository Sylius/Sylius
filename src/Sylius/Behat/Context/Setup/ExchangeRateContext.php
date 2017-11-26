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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
    ): void {
        $exchangeRate = $this->createExchangeRate($sourceCurrency, $targetCurrency, $ratio);

        $this->saveExchangeRate($exchangeRate);
    }

    /**
     * @return ExchangeRateInterface
     */
    private function createExchangeRate(CurrencyInterface $sourceCurrency, CurrencyInterface $targetCurrency, float $ratio = 1.00): ExchangeRateInterface
    {
        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $this->exchangeRateFactory->createNew();
        $exchangeRate->setSourceCurrency($sourceCurrency);
        $exchangeRate->setTargetCurrency($targetCurrency);
        $exchangeRate->setRatio((float) $ratio);

        return $exchangeRate;
    }

    private function saveExchangeRate(ExchangeRateInterface $exchangeRate): void
    {
        $this->exchangeRateRepository->add($exchangeRate);
        $this->sharedStorage->set('exchange_rate', $exchangeRate);
    }
}
