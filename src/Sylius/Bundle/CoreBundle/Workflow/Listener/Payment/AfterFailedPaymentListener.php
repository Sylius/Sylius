<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Payment;

use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterAuthorizedPaymentCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Payment\AfterFailedPaymentCallbackInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterFailedPaymentListener
{
    /** @param AfterFailedPaymentCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterFailedPaymentCallbackInterface::class);
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
