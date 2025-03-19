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
use Webmozart\Assert\Assert;

/** @experimental */
final class CompositeNotifyPaymentProvider implements NotifyPaymentProviderInterface
{
    /**
     * @param iterable<NotifyPaymentProviderInterface> $paymentNotifyProviders
     */
    public function __construct(
        private iterable $paymentNotifyProviders,
    ) {
        Assert::allIsInstanceOf($this->paymentNotifyProviders, NotifyPaymentProviderInterface::class);
    }

    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface
    {
        foreach ($this->paymentNotifyProviders as $provider) {
            if ($provider->supports($request, $paymentMethod)) {
                return $provider->getPayment($request, $paymentMethod);
            }
        }

        throw new \LogicException(sprintf(
            'No payment notify provider found supporting this request and payment method (code: %s).',
            $paymentMethod->getCode(),
        ));
    }

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool
    {
        return true;
    }
}
