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
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    public function __construct(
        private CurrencyNameConverterInterface $currencyNameConverter,
        private RepositoryInterface $currencyRepository,
        private ExchangeRateRepositoryInterface $exchangeRateRepository,
    ) {
    }

    /**
     * @Transform /^exchange rate between "([^"]+)" and "([^"]+)"$/
     */
    public function getExchangeRateByCurrencies(
        string $sourceCurrencyName,
        string $targetCurrencyName,
    ): ExchangeRateInterface {
        $sourceCurrencyCode = $this->currencyNameConverter->convertToCode($sourceCurrencyName);
        $targetCurrencyCode = $this->currencyNameConverter->convertToCode($targetCurrencyName);

        /** @var ExchangeRateInterface|null $exchangeRate */
        $exchangeRate = $this
            ->exchangeRateRepository
            ->findOneWithCurrencyPair($sourceCurrencyCode, $targetCurrencyCode)
        ;

        Assert::notNull(
            $exchangeRate,
            sprintf(
                'ExchangeRate for %s and %s currencies does not exist.',
                $sourceCurrencyName,
                $targetCurrencyName,
            ),
        );

        return $exchangeRate;
    }
}
