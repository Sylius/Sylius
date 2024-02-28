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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Payum\CommandProvider;

use Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Payum\Command\AuthorizePaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class AuthorizeCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getType() === PaymentRequestInterface::DATA_TYPE_AUTHORIZE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new AuthorizePaymentRequest($paymentRequest->getHash());
    }
}
