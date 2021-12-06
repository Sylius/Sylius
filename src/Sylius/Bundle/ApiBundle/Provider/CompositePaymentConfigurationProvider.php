<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Provider;

use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

/** @experimental */
final class CompositePaymentConfigurationProvider implements CompositePaymentConfigurationProviderInterface
{
    /** @var iterable<PaymentConfigurationProviderInterface> */
    private iterable $apiPaymentMethodHandlers;

    public function __construct(iterable $apiPaymentMethodHandlers)
    {
        $this->apiPaymentMethodHandlers = $apiPaymentMethodHandlers;
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
