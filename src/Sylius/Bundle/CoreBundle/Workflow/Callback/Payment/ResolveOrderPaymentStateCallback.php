<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use Sylius\Bundle\CoreBundle\Workflow\StateResolver\OrderPaymentStateResolverInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ResolveOrderPaymentStateCallback implements AfterCompletedPaymentCallbackInterface, AfterProcessedPaymentCallbackInterface, AfterRefundedPaymentCallbackInterface, AfterAuthorizedPaymentCallbackInterface
{
    public function __construct(private OrderPaymentStateResolverInterface $orderPaymentStateResolver)
    {
    }

    public function call(PaymentInterface $payment): void
    {
       $this->orderPaymentStateResolver->resolve($payment->getOrder());
    }
}
