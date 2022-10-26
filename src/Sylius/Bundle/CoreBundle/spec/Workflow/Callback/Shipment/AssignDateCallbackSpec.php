<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\AssignDateCallback;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class AssignDateCallbackSpec extends ObjectBehavior
{
    function let(ShippingDateAssignerInterface $shippingDateAssigner): void
    {
        $this->beConstructedWith($shippingDateAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignDateCallback::class);
    }

    function it_assigns_date_for_shipping(
        ShipmentInterface $shipment,
        ShippingDateAssignerInterface $shippingDateAssigner,
    ): void {
        $shippingDateAssigner->assign($shipment)->shouldBeCalled();

        $this->call($shipment);
    }
}
