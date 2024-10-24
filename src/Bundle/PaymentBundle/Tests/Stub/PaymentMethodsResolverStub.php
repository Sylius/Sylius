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

namespace Sylius\Bundle\PaymentBundle\Tests\Stub;

use Sylius\Bundle\PaymentBundle\Attribute\AsPaymentMethodsResolver;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

#[AsPaymentMethodsResolver(type: 'test', label: 'Test', priority: 15)]
final class PaymentMethodsResolverStub implements PaymentMethodsResolverInterface
{
    public function getSupportedMethods(PaymentInterface $subject): array
    {
        return [];
    }

    public function supports(PaymentInterface $subject): bool
    {
        return true;
    }
}
