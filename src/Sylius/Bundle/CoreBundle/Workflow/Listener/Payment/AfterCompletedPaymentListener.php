<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Payment;

use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterCompletedPaymentCallbackInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterCompletedPaymentListener
{
    /** @param AfterCompletedPaymentCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
    }

    public function call(Event $event): void
    {
        /** @var PaymentInterface $payment */
        $payment = $event->getSubject();

        foreach ($this->callbacks as $callback) {
            $callback->call($payment);
        }
    }
}
