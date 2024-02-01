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

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

final class PaymentRequestTypeCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function __construct(
        private ServiceProviderInterface $locator,
    ) {
    }

    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        /** @var PaymentRequestCommandProviderInterface $provider */
        $provider = $this->locator->get($paymentRequest->getType());

        return $provider->supports($paymentRequest);
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        /** @var PaymentRequestCommandProviderInterface $provider */
        $provider = $this->locator->get($paymentRequest->getType());

        return $provider->provide($paymentRequest);
    }
}
