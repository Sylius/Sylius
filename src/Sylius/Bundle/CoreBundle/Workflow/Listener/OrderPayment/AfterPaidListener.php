<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderPayment;

use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterAddressedCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment\AfterPaidCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterPaidListener
{
    /** @param AfterPaidCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterPaidCallbackInterface::class);
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
