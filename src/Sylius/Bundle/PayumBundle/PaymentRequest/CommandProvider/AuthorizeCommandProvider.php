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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\CommandProvider;

use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Command\AuthorizePaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class AuthorizeCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_AUTHORIZE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new AuthorizePaymentRequest($paymentRequest->getHash()?->toBinary());
    }
}
