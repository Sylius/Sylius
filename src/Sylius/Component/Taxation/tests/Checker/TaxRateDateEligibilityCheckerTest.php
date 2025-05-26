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

namespace Tests\Sylius\Component\Taxation\Checker;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityChecker;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Symfony\Component\Clock\ClockInterface;

final class TaxRateDateEligibilityCheckerTest extends TestCase
{
    /** @var ClockInterface&MockObject */
    private MockObject $clock;

    /** @var TaxRateInterface&MockObject */
    private MockObject $taxRate;

    private TaxRateDateEligibilityChecker $taxRateDateEligibilityChecker;

    protected function setUp(): void
    {
        $this->clock = $this->createMock(ClockInterface::class);
        $this->taxRate = $this->createMock(TaxRateInterface::class);
        $this->taxRateDateEligibilityChecker = new TaxRateDateEligibilityChecker($this->clock);
    }

    public function testShouldImplementTaxRateResolverInterface(): void
    {
        $this->assertInstanceOf(TaxRateDateEligibilityCheckerInterface::class, $this->taxRateDateEligibilityChecker);
    }

    #[DataProvider('provideBothDatesDefinedData')]
    public function testShouldCanBeInDateWhenBothDatesAreDefined(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        bool $expectedResult,
    ): void {
        $this->clock->expects($this->once())->method('now')->willReturn(new DateTimeImmutable('01-02-2022'));
        $this->taxRate->expects($this->once())->method('getStartDate')->willReturn($startDate);
        $this->taxRate->expects($this->once())->method('getEndDate')->willReturn($endDate);

        $this->assertSame($expectedResult, $this->taxRateDateEligibilityChecker->isEligible($this->taxRate));
    }

    /** @return iterable<array<DateTimeImmutable|bool>> */
    public static function provideBothDatesDefinedData(): iterable
    {
        yield [new DateTimeImmutable('01-01-2022'), new DateTimeImmutable('01-03-2022'), true];
        yield [new DateTimeImmutable('01-01-2012'), new DateTimeImmutable('21-01-2022'), false];
    }

    #[DataProvider('provideOnlyStartDateDefinedData')]
    public function testShouldCanBeInDateWhenOnlyStartDateIsDefined(
        DateTimeImmutable $startDate,
        bool $expectedResult,
    ): void {
        $this->clock->expects($this->once())->method('now')->willReturn(new DateTimeImmutable('01-02-2022'));
        $this->taxRate->expects($this->once())->method('getStartDate')->willReturn($startDate);
        $this->taxRate->expects($this->once())->method('getEndDate')->willReturn(null);

        $this->assertSame($expectedResult, $this->taxRateDateEligibilityChecker->isEligible($this->taxRate));
    }

    /** @return iterable<array<DateTimeImmutable|bool>> */
    public static function provideOnlyStartDateDefinedData(): iterable
    {
        yield [new DateTimeImmutable('01-01-2022'), true];
        yield [new DateTimeImmutable('21-09-2029'), false];
    }

    #[DataProvider('provideOnlyEndDateDefinedData')]
    public function testShouldBeInDateWhenOnlyEndDateIsDefined(
        DateTimeImmutable $endDate,
        bool $expectedResult,
    ): void {
        $this->clock->expects($this->once())->method('now')->willReturn(new DateTimeImmutable('01-02-2022'));
        $this->taxRate->expects($this->once())->method('getStartDate')->willReturn(null);
        $this->taxRate->expects($this->once())->method('getEndDate')->willReturn($endDate);

        $this->assertSame($expectedResult, $this->taxRateDateEligibilityChecker->isEligible($this->taxRate));
    }

    /** @return iterable<array<DateTimeImmutable|bool>> */
    public static function provideOnlyEndDateDefinedData(): iterable
    {
        yield [new DateTimeImmutable('01-01-2022'), false];
        yield [new DateTimeImmutable('21-09-2029'), true];
    }
}
