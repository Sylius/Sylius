<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\CoreBundle\EventListener\AdjustmentSubscriber;
use Sylius\Component\Core\Model\InventoryUnit;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Piotr Walków <walkow.piotr@gmail.com>
 */
class AdjustmentSubscriberSpec extends ObjectBehavior
{
    function let(FactoryInterface $adjustmentFactory)
    {
        $this->beConstructedWith(
            $adjustmentFactory
        );
    }

    function it_is_initializable() {
        $this->shouldHaveType(AdjustmentSubscriber::class);
    }

    function it_add_adjustment_on_order(
        OrderInterface $order,
        AdjustmentEvent $event,
        AdjustmentInterface $adjustment,
        AdjustmentDTO $adjustmentDTO,
        FactoryInterface $adjustmentFactory
    ) {
        $event->getSubject()
            ->shouldBeCalled()
            ->willReturn($order);

        $event->getArgument('adjustment-data')
            ->shouldBeCalled()
            ->willReturn($adjustmentDTO);

        $adjustmentFactory->createNew()->willReturn($adjustment);

        $adjustment->setType(Argument::any())->shouldBeCalled();
        $adjustment->setAmount(Argument::any())->shouldBeCalled();
        $adjustment->setDescription(Argument::any())->shouldBeCalled();
        $adjustment->setNeutral(Argument::any())->shouldBeCalled();
        $adjustment->setOriginId(Argument::any())->shouldBeCalled();
        $adjustment->setOriginType(Argument::any())->shouldBeCalled();
        $adjustment->setAdjustable($order)->shouldBeCalled();

        $order
            ->addAdjustment($adjustment)
            ->shouldBeCalled();

        $this->addAdjustmentToOrder($event);
    }

    function it_throws_exception_on_non_order_while_adding_on_order(
        AdjustmentEvent $event
    ) {
        $order = new \StdClass();

        $event->getSubject()->willReturn($order);

        $this->shouldThrow(\UnexpectedValueException::class)->during('addAdjustmentToOrder',[$event]);
    }

    function it_add_adjustment_on_inventory_unit(
        InventoryUnit $inventoryUnit,
        AdjustmentEvent $event,
        AdjustmentInterface $adjustment,
        AdjustmentDTO $adjustmentDTO,
        FactoryInterface $adjustmentFactory
    ) {
        $event->getSubject()->willReturn($inventoryUnit);
        $event->getArgument('adjustment-data')->willReturn($adjustmentDTO);
        $adjustmentFactory->createNew()->willReturn($adjustment);

        $adjustment->setType(Argument::any())->shouldBeCalled();
        $adjustment->setAmount(Argument::any())->shouldBeCalled();
        $adjustment->setDescription(Argument::any())->shouldBeCalled();
        $adjustment->setNeutral(Argument::any())->shouldBeCalled();
        $adjustment->setOriginId(Argument::any())->shouldBeCalled();
        $adjustment->setOriginType(Argument::any())->shouldBeCalled();
        $adjustment->setAdjustable($inventoryUnit)->shouldBeCalled();

        $inventoryUnit->addAdjustment($adjustment)->shouldBeCalled();

        $this->addAdjustmentToInventoryUnit($event);
    }

    function it_throws_exception_on_non_invenory_unit_while_adding_on_inventory_unit(
        AdjustmentEvent $event
    ) {
        $nonInventoryUnit = new \StdClass();

        $event->getSubject()->willReturn($nonInventoryUnit);

        $this->shouldThrow(\UnexpectedValueException::class)->during('addAdjustmentToInventoryUnit',[$event]);
    }

}
