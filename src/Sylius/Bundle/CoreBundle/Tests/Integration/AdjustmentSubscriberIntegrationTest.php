<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Integration;

use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\CoreBundle\EventListener\AdjustmentSubscriber;
use Sylius\Bundle\CoreBundle\Tests\IntegrationTestCase;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Prophecy\Prophecy;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\AdjustmentDTO;

/**
 * @author  Piotr Walków <walkowpiotr@gmail.com>
 */
class AdjustmentSubscriberIntegrationTest extends IntegrationTestCase
{
    public function test_it_listens_to_adjustment_on_order_event()
    {
        $order = $this->mockOrder();

        $adjustmentDTO = new AdjustmentDTO();
        $adjustmentDTO->amount = 123;

        $adjustmentEvent = new AdjustmentEvent(
            $order->reveal(),
            [AdjustmentSubscriber::EVENT_ARGUMENT_DATA_KEY => $adjustmentDTO]
        );

        $order->addAdjustment(Argument::type(Adjustment::class))->shouldBeCalled();

        $this->eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
            $adjustmentEvent
        );
    }

    public function test_it_listens_to_adjustment_on_inventory_unit_events()
    {
        $inventoryUnit = $this->mockInventoryUnit();
        $adjustmentDTO = new AdjustmentDTO();
        $adjustmentDTO->amount = 123;

        $adjustmentEvent = new AdjustmentEvent(
            $inventoryUnit->reveal(),
            [AdjustmentSubscriber::EVENT_ARGUMENT_DATA_KEY => $adjustmentDTO]
        );

        $inventoryUnit->addAdjustment(Argument::type(Adjustment::class))->shouldBeCalled();

        $this->eventDispatcher->dispatch(
            AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT,
            $adjustmentEvent
        );
    }

    /**
     * @return Prophecy\ObjectProphecy|OrderInterface
     */
    private function mockOrder()
    {
        return $this->prophet->prophesize(OrderInterface::class);
    }

    /**
     * @return Prophecy\ObjectProphecy|InventoryUnitInterface
     */
    private function mockInventoryUnit()
    {
        return $this->prophet->prophesize(InventoryUnitInterface::class);
    }
}
