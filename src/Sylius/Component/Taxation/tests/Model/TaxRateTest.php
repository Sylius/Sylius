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

namespace Tests\Sylius\Component\Taxation\Model;

use DateTime;
use DateTimeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRate;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class TaxRateTest extends TestCase
{
    /** @var TaxCategoryInterface&MockObject */
    private MockObject $taxCategory;

    private TaxRate $taxRate;

    protected function setUp(): void
    {
        $this->taxCategory = $this->createMock(TaxCategoryInterface::class);
        $this->taxRate = new TaxRate();
    }

    public function testShouldImplementTaxRateInterface(): void
    {
        $this->assertInstanceOf(TaxRateInterface::class, $this->taxRate);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->taxRate->getId());
    }

    public function testShoulddNotBelongToCategoryByDefault(): void
    {
        $this->assertNull($this->taxRate->getCategory());
    }

    public function testShouldAllowAssignItSelfToCategory(): void
    {
        $this->taxRate->setCategory($this->taxCategory);

        $this->assertSame($this->taxCategory, $this->taxRate->getCategory());
    }

    public function testShouldAllowDetachItselfFromCategory(): void
    {
        $this->taxRate->setCategory($this->taxCategory);

        $this->taxRate->setCategory(null);

        $this->assertNull($this->taxRate->getCategory());
    }

    public function testShouldBeUnnamedByDefault(): void
    {
        $this->assertNull($this->taxRate->getName());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->taxRate->setName('Taxable goods');

        $this->assertSame('Taxable goods', $this->taxRate->getName());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->taxRate->setCode('TR1');

        $this->assertSame('TR1', $this->taxRate->getCode());
    }

    public function testShouldHaveAmountEqualToZeroByDefault(): void
    {
        $this->assertEquals(0.00, $this->taxRate->getAmount());
    }

    public function testShouldAmountBeMutable(): void
    {
        $this->taxRate->setAmount(0.23);

        $this->assertEquals(0.23, $this->taxRate->getAmount());
    }

    #[DataProvider('provideAmountData')]
    public function testShouldRepresentAmountAsPercentage(float $amount, float $expectedPercentage): void
    {
        $this->taxRate->setAmount($amount);

        $this->assertEquals($expectedPercentage, $this->taxRate->getAmountAsPercentage());
    }

    /** @return iterable<float[]> */
    public static function provideAmountData(): iterable
    {
        yield [0.23, 23.00];
        yield [0.125, 12.5];
    }

    public function testShouldNotBeIncludedInPriceByDefault(): void
    {
        $this->assertFalse($this->taxRate->isIncludedInPrice());
    }

    public function testShouldInclusionInPriceBeMutable(): void
    {
        $this->taxRate->setIncludedInPrice(true);

        $this->assertTrue($this->taxRate->isIncludedInPrice());
    }

    public function testShouldNotHaveCalculatorDefinedByDefault(): void
    {
        $this->assertNull($this->taxRate->getCalculator());
    }

    public function testShouldCalculatorBeMutable(): void
    {
        $this->taxRate->setCalculator('default');

        $this->assertSame('default', $this->taxRate->getCalculator());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        $this->assertInstanceOf(DateTimeInterface::class, $this->taxRate->getCreatedAt());
    }

    public function testShouldNotHaveLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->taxRate->getUpdatedAt());
    }

    public function testShouldHaveLabel(): void
    {
        $this->taxRate->setName('Test tax');
        $this->taxRate->setAmount(0.23);

        $this->assertSame('Test tax (23%)', $this->taxRate->getLabel());
    }

    public function testShouldHaveStartDate(): void
    {
        $startDate = new DateTime('01-01-2022');

        $this->taxRate->setStartDate($startDate);

        $this->assertSame($startDate, $this->taxRate->getStartDate());
    }

    public function testShouldHaveEndDate(): void
    {
        $endDate = new DateTime('01-01-2022');

        $this->taxRate->setEndDate($endDate);

        $this->assertSame($endDate, $this->taxRate->getEndDate());
    }
}
