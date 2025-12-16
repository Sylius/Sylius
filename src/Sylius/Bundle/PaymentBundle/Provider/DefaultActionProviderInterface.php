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

use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/** @experimental */
interface DefaultActionProviderInterface
{
    public function getAction(PaymentRequestInterface $paymentRequest): string;

    public function getActionFromPaymentMethodCode(string $paymentMethodCode, ?string $defaultAction = null): string;

    public function getActionFromPaymentMethod(PaymentMethodInterface $paymentMethod, ?string $defaultAction = null): string;
}
