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

namespace Sylius\Component\Core\Test\Tests\Taxation\Applicator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributor;
use Sylius\Component\Core\Model\Adjustment;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\TaxRate;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Taxation\Calculator\DecimalCalculator;
use Sylius\Component\Taxation\Calculator\DefaultCalculator;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Sylius\Resource\Factory\Factory;
use Sylius\Resource\Factory\FactoryInterface;

final class OrderItemUnitsTaxesApplicatorTest extends TestCase
{
    public function test_it_calculates_tax_with_rounding(): void
    {
        /** @var FactoryInterface<AdjustmentInterface> $adjustmentFactory */
        $adjustmentFactory = new Factory(Adjustment::class);

        $applicator = new OrderItemUnitsTaxesApplicator(
            new DefaultCalculator(),
            new AdjustmentFactory($adjustmentFactory),
            $this->createConfiguredMock(TaxRateResolverInterface::class, [
                'resolve' => $this->createTaxRate(),
            ]),
            new ProportionalIntegerDistributor(),
        );

        $order = $this->createOrder();

        $applicator->apply($order, new Zone());

        $this->assertEquals(3940, $order->getTotal());
        $this->assertEquals(656, $order->getTaxTotal());
    }

    public function test_it_calculates_tax_with_decimal_precision(): void
    {
        /** @var FactoryInterface<AdjustmentInterface> $adjustmentFactory */
        $adjustmentFactory = new Factory(Adjustment::class);

        $applicator = new OrderItemUnitsTaxesApplicator(
            new DecimalCalculator(),
            new AdjustmentFactory($adjustmentFactory),
            $this->createConfiguredMock(TaxRateResolverInterface::class, [
                'resolve' => $this->createTaxRate(),
            ]),
            new ProportionalIntegerDistributor(),
        );

        $order = $this->createOrder();

        $applicator->apply($order, new Zone());

        $this->assertEquals(3940, $order->getTotal());
        $this->assertEquals(657, $order->getTaxTotal());
    }

    private function createOrder(): OrderInterface
    {
        $item = new OrderItem();
        $item->setVariant(new ProductVariant());
        $item->setUnitPrice(1970);
        $item->addUnit(new OrderItemUnit($item));
        $item->addUnit(new OrderItemUnit($item));

        $order = new Order();
        $order->addItem($item);

        return $order;
    }

    private function createTaxRate(): TaxRateInterface
    {
        $taxRate = new TaxRate();
        $taxRate->setCode('standard');
        $taxRate->setName('Standard');
        $taxRate->setAmount(0.2);
        $taxRate->setIncludedInPrice(true);

        return $taxRate;
    }
}
