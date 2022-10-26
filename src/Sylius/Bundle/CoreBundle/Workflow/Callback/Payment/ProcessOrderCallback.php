<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class ProcessOrderCallback implements AfterFailedPaymentCallbackInterface, AfterCanceledPaymentCallbackInterface
{
    public function __construct(private OrderProcessorInterface $orderProcessor)
    {
    }

    public function call(PaymentInterface $payment): void
    {
        $order = $payment->getOrder();
        Assert::notNull($order);

        $this->orderProcessor->process($order);
    }
}
