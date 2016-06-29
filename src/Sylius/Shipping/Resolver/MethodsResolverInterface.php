<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Shipping\Resolver;

use Sylius\Shipping\Model\ShippingMethodInterface;
use Sylius\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface MethodsResolverInterface
{
    /**
     * @param ShippingSubjectInterface $subject
     *
     * @return ShippingMethodInterface[]
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject);

    /**
     * @param ShippingSubjectInterface $subject
     *
     * @return bool
     */
    public function supports(ShippingSubjectInterface $subject);
}
