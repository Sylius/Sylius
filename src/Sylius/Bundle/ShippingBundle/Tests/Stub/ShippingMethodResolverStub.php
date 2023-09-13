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

namespace Sylius\Bundle\ShippingBundle\Tests\Stub;

use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodResolver;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

#[AsShippingMethodResolver(type: 'test', label: 'Test', priority: 10)]
final class ShippingMethodResolverStub implements ShippingMethodsResolverInterface
{
    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        return [];
    }

    public function supports(ShippingSubjectInterface $subject): bool
    {
        return true;
    }
}
