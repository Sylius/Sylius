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

namespace Tests\Sylius\Component\Currency\Context;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Currency\Context\CompositeCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

final class CompositeCurrencyContextTest extends TestCase
{
    private CompositeCurrencyContext $compositeCurrencyContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compositeCurrencyContext = new CompositeCurrencyContext();
    }

    public function testShouldImplementCurrencyContextInterface(): void
    {
        self::assertInstanceOf(CurrencyContextInterface::class, $this->compositeCurrencyContext);
    }

    public function testShouldThrowCurrencyNotFoundExceptionIfNoNestedCurrencyContextsAreDefined(): void
    {
        self::expectException(CurrencyNotFoundException::class);

        $this->compositeCurrencyContext->getCurrencyCode();
    }

    public function testShouldThrowCurrencyNotFoundExceptionIfNoneOfTheNestedContextsReturnACurrency(): void
    {
        $currencyContextMock = $this->createMock(CurrencyContextInterface::class);
        $currencyContextMock
            ->method('getCurrencyCode')
            ->will($this->throwException(new CurrencyNotFoundException()));

        $this->compositeCurrencyContext->addContext($currencyContextMock);

        self::expectException(CurrencyNotFoundException::class);
        $this->compositeCurrencyContext->getCurrencyCode();
    }

    public function testShouldReturnFirstResultReturnedByNestedRequestResolvers(): void
    {
        $firstCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);
        $secondCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);
        $thirdCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);

        $firstCurrencyContextMock
            ->method('getCurrencyCode')
            ->will($this->throwException(new CurrencyNotFoundException()));
        $secondCurrencyContextMock
            ->method('getCurrencyCode')
            ->willReturn('BTC');
        $thirdCurrencyContextMock
            ->expects(self::never())
            ->method('getCurrencyCode');

        $this->compositeCurrencyContext->addContext($firstCurrencyContextMock);
        $this->compositeCurrencyContext->addContext($secondCurrencyContextMock);
        $this->compositeCurrencyContext->addContext($thirdCurrencyContextMock);

        self::assertSame('BTC', $this->compositeCurrencyContext->getCurrencyCode());
    }

    public function testNestedRequestResolversCanHavePriority(): void
    {
        $firstCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);
        $secondCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);
        $thirdCurrencyContextMock = $this->createMock(CurrencyContextInterface::class);

        $firstCurrencyContextMock
            ->expects(self::never())
            ->method('getCurrencyCode');
        $secondCurrencyContextMock
            ->method('getCurrencyCode')
            ->willReturn('BTC');
        $thirdCurrencyContextMock
            ->method('getCurrencyCode')
            ->will($this->throwException(new CurrencyNotFoundException()));

        $this->compositeCurrencyContext->addContext($firstCurrencyContextMock, -5);
        $this->compositeCurrencyContext->addContext($secondCurrencyContextMock, 0);
        $this->compositeCurrencyContext->addContext($thirdCurrencyContextMock, 5);

        self::assertSame('BTC', $this->compositeCurrencyContext->getCurrencyCode());
    }
}
