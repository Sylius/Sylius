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

namespace Tests\Sylius\Component\Currency\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRate;
use Sylius\Component\Currency\Model\ExchangeRateInterface;

final class ExchangeRateTest extends TestCase
{
    private ExchangeRate $exchangeRate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->exchangeRate = new ExchangeRate();
    }

    public function testShouldImplementExchangeRateInterface(): void
    {
        self::assertInstanceOf(ExchangeRateInterface::class, $this->exchangeRate);
    }

    public function testShouldHaveARatio(): void
    {
        self::assertNull($this->exchangeRate->getRatio());

        $this->exchangeRate->setRatio(1.02);
        self::assertSame(1.02, $this->exchangeRate->getRatio());

        $this->exchangeRate->setRatio(1e-6);
        self::assertSame(1e-6, $this->exchangeRate->getRatio());
    }

    public function testShouldHaveBaseCurrency(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        self::assertNull($this->exchangeRate->getSourceCurrency());

        $this->exchangeRate->setSourceCurrency($currency);
        self::assertSame($currency, $this->exchangeRate->getSourceCurrency());
    }

    public function testShouldHaveTargetCurrency(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        self::assertNull($this->exchangeRate->getTargetCurrency());

        $this->exchangeRate->setTargetCurrency($currency);
        self::assertSame($currency, $this->exchangeRate->getTargetCurrency());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->exchangeRate->getCreatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();

        $this->exchangeRate->setCreatedAt($date);
        self::assertSame($date, $this->exchangeRate->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->exchangeRate->getUpdatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();

        $this->exchangeRate->setUpdatedAt($date);
        self::assertSame($date, $this->exchangeRate->getUpdatedAt());
    }
}
