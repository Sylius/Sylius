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

namespace Tests\Sylius\Component\Currency\Converter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class CurrencyConverterTest extends TestCase
{
    private CurrencyConverter $currencyConverter;

    /** @var ExchangeRateRepositoryInterface<ExchangeRateInterface>&MockObject */
    private ExchangeRateRepositoryInterface $exchangeRateRepository;

    protected function setUp(): void
    {
        $this->exchangeRateRepository = $this->createMock(ExchangeRateRepositoryInterface::class);
        $this->currencyConverter = new CurrencyConverter($this->exchangeRateRepository);
    }

    public function testItImplementsACurrencyConverterInterface(): void
    {
        $this->assertInstanceOf(CurrencyConverterInterface::class, $this->currencyConverter);
    }

    public function testItConvertsMultiplyingRatioBasedOnCurrencyPairExchangeRate(): void
    {
        $sourceCurrency = $this->createMock(CurrencyInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);

        $this->exchangeRateRepository
            ->expects(self::once())
            ->method('findOneWithCurrencyPair')
            ->with('GBP', 'USD')
            ->willReturn($exchangeRate);

        $exchangeRate
            ->expects(self::once())
            ->method('getRatio')
            ->willReturn(1.30);

        $exchangeRate
            ->expects(self::once())
            ->method('getSourceCurrency')
            ->willReturn($sourceCurrency);

        $sourceCurrency
            ->expects(self::once())
            ->method('getCode')
            ->willReturn('GBP');

        $result = $this->currencyConverter->convert(666, 'GBP', 'USD');

        self::assertEquals(866, $result);
    }

    public function testItConvertsDividingRatioBasedOnReversedCurrencyPairExchangeRate(): void
    {
        $sourceCurrency = $this->createMock(CurrencyInterface::class);
        $exchangeRate = $this->createMock(ExchangeRateInterface::class);

        $this->exchangeRateRepository
            ->expects(self::once())
            ->method('findOneWithCurrencyPair')
            ->with('GBP', 'USD')
            ->willReturn($exchangeRate);

        $exchangeRate
            ->expects(self::once())
            ->method('getRatio')
            ->willReturn(1.30);

        $exchangeRate
            ->expects(self::once())
            ->method('getSourceCurrency')
            ->willReturn($sourceCurrency);

        $sourceCurrency
            ->expects(self::once())
            ->method('getCode')
            ->willReturn('USD');

        $result = $this->currencyConverter->convert(666, 'GBP', 'USD');

        self::assertEquals(512, $result);
    }

    public function testItReturnsGivenValueIfExchangeRateForGivenCurrencyPairHasNotBeenFound(): void
    {
        $this->exchangeRateRepository
            ->expects(self::once())
            ->method('findOneWithCurrencyPair')
            ->with('GBP', 'USD')
            ->willReturn(null);

        $result = $this->currencyConverter->convert(666, 'GBP', 'USD');

        self::assertEquals(666, $result);
    }

    public function testItReturnsGivenValueIfBothCurrenciesInCurrencyPairAreTheSame(): void
    {
        $this->exchangeRateRepository
            ->expects(self::never())
            ->method('findOneWithCurrencyPair');

        $result = $this->currencyConverter->convert(666, 'GBP', 'GBP');

        self::assertEquals(666, $result);
    }
}
