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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private FactoryInterface $exchangeRateFactory,
        private ExchangeRateRepositoryInterface $exchangeRateRepository,
    ) {
    }

    /**
     * @Given the exchange rate of :sourceCurrency to :targetCurrency is :ratio
     */
    public function thereIsAnExchangeRateWithSourceCurrencyAndTargetCurrency(
        CurrencyInterface $sourceCurrency,
        CurrencyInterface $targetCurrency,
        $ratio,
    ) {
        $exchangeRate = $this->createExchangeRate($sourceCurrency, $targetCurrency, $ratio);

        $this->saveExchangeRate($exchangeRate);
    }

    /**
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

    private function saveExchangeRate(ExchangeRateInterface $exchangeRate)
    {
        $this->exchangeRateRepository->add($exchangeRate);
        $this->sharedStorage->set('exchange_rate', $exchangeRate);
    }
}
