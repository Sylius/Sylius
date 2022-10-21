<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderShipping;

use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping\AfterShippedOrderCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterShippedOrderListener
{
    /** @param AfterShippedOrderCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
    }

    public function call(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($this->callbacks as $callback) {
            $callback->call($order);
        }
    }
}
