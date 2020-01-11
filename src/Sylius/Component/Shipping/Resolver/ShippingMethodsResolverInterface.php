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

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

interface ShippingMethodsResolverInterface
{
    /**
     * @return ShippingMethodInterface[]
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject): array;

    public function supports(ShippingSubjectInterface $subject): bool;
}
