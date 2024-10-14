<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
interface NotifyPaymentProviderInterface
{
    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface;

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool;
}
