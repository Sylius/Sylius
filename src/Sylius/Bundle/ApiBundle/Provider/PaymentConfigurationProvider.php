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

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

/** @experimental */
final class PaymentConfigurationProvider
{
    /** @var iterable */
    private $apiPayments;

    public function __construct(iterable $apiPayments)
    {
        $this->apiPayments = $apiPayments;
    }

    public function provide(PaymentInterface $payment): array
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        foreach ($this->apiPayments as $apiPayment) {
            if ($apiPayment->supports($paymentMethod)) {
                return $apiPayment->provideConfiguration($payment);
            }
        }

        return [];
    }
}
