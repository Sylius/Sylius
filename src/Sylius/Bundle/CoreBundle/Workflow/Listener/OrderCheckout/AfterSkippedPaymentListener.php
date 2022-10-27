<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterSkippedPaymentCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterSkippedPaymentListener
{
    /** @param AfterSkippedPaymentCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterSkippedPaymentCallbackInterface::class);
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
