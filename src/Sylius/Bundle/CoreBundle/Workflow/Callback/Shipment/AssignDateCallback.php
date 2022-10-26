<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment;

use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class AssignDateCallback implements BeforeShippedCallbackInterface
{
    public function __construct(private ShippingDateAssignerInterface $shippingDateAssigner)
    {
    }

    public function call(ShipmentInterface $shipment): void
    {
        $this->shippingDateAssigner->assign($shipment);
    }
}
