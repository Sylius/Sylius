<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment;

use Sylius\Component\Core\Model\ShipmentInterface;

interface AfterShippedCallbackInterface
{
    public function call(ShipmentInterface $shipment): void;
}
