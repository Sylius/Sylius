<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Shipment;

use Sylius\Bundle\CoreBundle\Workflow\Callback\Shipment\BeforeShippedCallbackInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class BeforeShippedListener
{
    /** @param BeforeShippedCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, BeforeShippedCallbackInterface::class);
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
