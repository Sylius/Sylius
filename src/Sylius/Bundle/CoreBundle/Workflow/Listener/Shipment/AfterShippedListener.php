<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Shipment;

use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\AfterShippedCallbackInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterShippedListener
{
    /** @param AfterShippedCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
    }

    public function call(Event $event): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $event->getSubject();

        foreach ($this->callbacks as $callback) {
            $callback->call($shipment);
        }
    }
}
