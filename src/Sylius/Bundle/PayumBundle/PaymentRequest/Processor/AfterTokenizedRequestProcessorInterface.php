<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Processor;

use Payum\Core\Security\TokenInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface AfterTokenizedRequestProcessorInterface
{
    public function process(
        PaymentRequestInterface $paymentRequest,
        TokenInterface $token,
    ): void;
}
