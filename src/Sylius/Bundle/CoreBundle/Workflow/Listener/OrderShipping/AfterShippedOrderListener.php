<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderShipping;

use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderShipping\AfterShippedOrderCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterShippedOrderListener
{
    /** @param AfterShippedOrderCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterShippedOrderCallbackInterface::class);
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
