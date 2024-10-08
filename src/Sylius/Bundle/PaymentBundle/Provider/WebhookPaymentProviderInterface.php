<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request;

interface WebhookPaymentProviderInterface {
    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface;

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool;
}
