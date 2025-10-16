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

namespace Tests\Sylius\Component\Locale\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Locale\Context\CompositeLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;

final class CompositeLocaleContextTest extends TestCase
{
    private CompositeLocaleContext $compositeLocaleContext;

    /** @var LocaleContextInterface&MockObject */
    private LocaleContextInterface $firstLocaleContext;

    /** @var LocaleContextInterface&MockObject */
    private LocaleContextInterface $secondLocaleContext;

    /** @var LocaleContextInterface&MockObject */
    private LocaleContextInterface $thirdLocaleContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->compositeLocaleContext = new CompositeLocaleContext();
        $this->firstLocaleContext = $this->createMock(LocaleContextInterface::class);
        $this->secondLocaleContext = $this->createMock(LocaleContextInterface::class);
        $this->thirdLocaleContext = $this->createMock(LocaleContextInterface::class);
    }

    public function testImplementsLocaleContextInterface(): void
    {
        self::assertInstanceOf(LocaleContextInterface::class, $this->compositeLocaleContext);
    }

    public function testThrowsALocaleNotFoundExceptionIfThereAreNoNestedLocaleContextsDefined(): void
    {
        self::expectException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->getLocaleCode();
    }

    public function testThrowsALocaleNotFoundExceptionIfNoneOfNestedLocaleContextsReturnedALocale(): void
    {
        $this->firstLocaleContext
            ->expects($this->once())
            ->method('getLocaleCode')
            ->willThrowException(new LocaleNotFoundException());
        $this->compositeLocaleContext->addContext($this->firstLocaleContext);
        self::expectException(LocaleNotFoundException::class);
        $this->compositeLocaleContext->getLocaleCode();
    }

    public function testReturnsFirstResultReturnedByNestedRequestResolvers(): void
    {
        $this->firstLocaleContext->expects($this->once())->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());
        $this->secondLocaleContext->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $this->thirdLocaleContext->expects($this->never())->method('getLocaleCode');
        $this->compositeLocaleContext->addContext($this->firstLocaleContext);
        $this->compositeLocaleContext->addContext($this->secondLocaleContext);
        $this->compositeLocaleContext->addContext($this->thirdLocaleContext);
        self::assertSame('en_US', $this->compositeLocaleContext->getLocaleCode());
    }

    public function testItsNestedRequestResolversCanHavePriority(): void
    {
        $this->firstLocaleContext->expects($this->never())->method('getLocaleCode');
        $this->secondLocaleContext->expects($this->once())->method('getLocaleCode')->willReturn('pl_PL');
        $this->thirdLocaleContext->expects($this->once())->method('getLocaleCode')->willThrowException(new LocaleNotFoundException());
        $this->compositeLocaleContext->addContext($this->firstLocaleContext, -5);
        $this->compositeLocaleContext->addContext($this->secondLocaleContext, 0);
        $this->compositeLocaleContext->addContext($this->thirdLocaleContext, 5);
        self::assertSame('pl_PL', $this->compositeLocaleContext->getLocaleCode());
    }
}
