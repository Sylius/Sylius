<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterPlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CreateShipmentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CreateShipmentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusShipmentWorkflow): void
    {
        $this->beConstructedWith($syliusShipmentWorkflow);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateShipmentCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_creates_shipment(
        OrderInterface $order,
        WorkflowInterface $syliusShipmentWorkflow,
        ShipmentInterface $firstShipment,
        ShipmentInterface $secondShipment,
    ): void {
        $order->getShipments()->willReturn(new ArrayCollection([
            $firstShipment->getWrappedObject(),
            $secondShipment->getWrappedObject(),
        ]));

        $syliusShipmentWorkflow->can($firstShipment, 'create')->willReturn(false);
        $syliusShipmentWorkflow->can($secondShipment, 'create')->willReturn(true);

        $syliusShipmentWorkflow->apply($firstShipment, 'create')->shouldNotBeCalled();
        $syliusShipmentWorkflow->apply($secondShipment, 'create')->shouldBeCalled();

        $this->call($order);
    }
}
