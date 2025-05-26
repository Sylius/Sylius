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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\TaxRate;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

final class TaxRateTest extends TestCase
{
    private TaxRate $taxRate;

    protected function setUp(): void
    {
        $this->taxRate = new TaxRate();
    }

    public function testShouldImplementTaxRateInterface(): void
    {
        $this->assertInstanceOf(TaxRateInterface::class, $this->taxRate);
    }

    public function testShouldExtendBaseTaxRateModel(): void
    {
        $this->assertInstanceOf(BaseTaxRate::class, $this->taxRate);
    }

    public function testShouldNotHaveAnyZoneDefinedByDefault(): void
    {
        $this->assertNull($this->taxRate->getZone());
    }

    public function testShouldZoneBeMutable(): void
    {
        $zone = $this->createMock(ZoneInterface::class);

        $this->taxRate->setZone($zone);

        $this->assertSame($zone, $this->taxRate->getZone());
    }
}
