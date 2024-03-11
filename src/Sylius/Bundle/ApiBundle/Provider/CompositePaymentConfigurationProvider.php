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

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class CompositePaymentConfigurationProvider implements CompositePaymentConfigurationProviderInterface
{
    public function __construct(private iterable $apiPaymentMethodHandlers)
    {
    }

    public function provide(PaymentInterface $payment): array
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        foreach ($this->apiPaymentMethodHandlers as $apiPaymentMethodHandler) {
            if ($apiPaymentMethodHandler->supports($paymentMethod)) {
                return $apiPaymentMethodHandler->provideConfiguration($payment);
            }
        }

        return [];
    }
}
