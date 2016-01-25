<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Taxation;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Taxation\OrderUnitsTaxesApplicatorInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderUnitsTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentsFactory,
        IntegerDistributorInterface $distributor
    ) {
        $this->beConstructedWith($calculator, $adjustmentsFactory, $distributor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Taxation\OrderUnitsTaxesApplicator');
    }

    function it_implements_order_shipment_taxes_applicator_interface()
    {
        $this->shouldImplement(OrderUnitsTaxesApplicatorInterface::class);
    }

    function it_applies_taxes_on_units_based_on_item_total_and_rate(
        $adjustmentsFactory,
        $calculator,
        $distributor,
        AdjustmentInterface $taxAdjustment1,
        AdjustmentInterface $taxAdjustment2,
        Collection $units,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        TaxRateInterface $taxRate
    ) {
        $taxRate->getLabel()->willReturn('Simple tax (10%)');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $orderItem->getTotal()->willReturn(1000);
        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $orderItem->getUnits()->willReturn($units);
        $units->isEmpty()->willReturn(false);
        $units->count()->willReturn(2);

        $units->get(0)->willReturn($unit1);
        $units->get(1)->willReturn($unit2);

        $distributor->distribute(100, 2)->willReturn(array(50, 50));

        $adjustmentsFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, 'Simple tax (10%)', 50, false)->willReturn($taxAdjustment1, $taxAdjustment2);

        $unit1->addAdjustment($taxAdjustment1)->shouldBeCalled();
        $unit2->addAdjustment($taxAdjustment2)->shouldBeCalled();

        $this->apply($orderItem, $taxRate);
    }

    function it_does_nothing_if_order_item_has_no_units(Collection $units, OrderItemInterface $orderItem, TaxRateInterface $taxRate)
    {
        $orderItem->getUnits()->willReturn($units);
        $units->isEmpty()->willReturn(true);

        $orderItem->getTotal()->shouldNotBeCalled();

        $this->apply($orderItem, $taxRate);
    }
}
