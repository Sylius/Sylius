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
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ExchangeRateContext implements Context
{
    /** @var CurrencyNameConverterInterface */
    private $currencyNameConverter;

    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var ExchangeRateRepositoryInterface */
    private $exchangeRateRepository;

    public function __construct(
        CurrencyNameConverterInterface $currencyNameConverter,
        RepositoryInterface $currencyRepository,
        ExchangeRateRepositoryInterface $exchangeRateRepository
    ) {
        $this->currencyNameConverter = $currencyNameConverter;
        $this->currencyRepository = $currencyRepository;
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    /**
     * @Transform /^exchange rate between "([^"]+)" and "([^"]+)"$/
     */
    public function getExchangeRateByCurrencies(
        string $sourceCurrencyName,
        string $targetCurrencyName
    ): ExchangeRateInterface {
        $sourceCurrencyCode = $this->currencyNameConverter->convertToCode($sourceCurrencyName);
        $targetCurrencyCode = $this->currencyNameConverter->convertToCode($targetCurrencyName);

        /** @var ExchangeRateInterface|null */
        $exchangeRate = $this
            ->exchangeRateRepository
            ->findOneWithCurrencyPair($sourceCurrencyCode, $targetCurrencyCode)
        ;

        Assert::notNull(
            $exchangeRate,
            sprintf(
                'ExchangeRate for %s and %s currencies does not exist.',
                $sourceCurrencyName,
                $targetCurrencyName
            )
        );

        return $exchangeRate;
    }
}
