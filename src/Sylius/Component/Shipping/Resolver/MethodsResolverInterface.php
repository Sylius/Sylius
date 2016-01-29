<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface MethodsResolverInterface
{
    /**
     * @param ShippingSubjectInterface $subject
     * @param array                    $criteria
     *
     * @return ShippingMethodInterface[]
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject, array $criteria = []);
}
