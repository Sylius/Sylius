<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Payment;

use Sylius\Component\Core\Model\PaymentInterface;

interface AfterCanceledPaymentCallbackInterface
{
    public function call(PaymentInterface $payment): void;
}
