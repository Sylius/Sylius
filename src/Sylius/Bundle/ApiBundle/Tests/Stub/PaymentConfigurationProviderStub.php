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

namespace Sylius\Bundle\ApiBundle\Tests\Stub;

use Sylius\Bundle\ApiBundle\Attribute\AsPaymentConfigurationProvider;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

#[AsPaymentConfigurationProvider(priority: 5)]
final class PaymentConfigurationProviderStub implements PaymentConfigurationProviderInterface
{
    public function supports(PaymentMethodInterface $paymentMethod): bool
    {
        return true;
    }

    public function provideConfiguration(PaymentInterface $payment): array
    {
        return [];
    }
}
