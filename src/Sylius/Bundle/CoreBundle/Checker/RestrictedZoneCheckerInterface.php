<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checker;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface RestrictedZoneCheckerInterface
{
    public function isRestricted(ProductInterface $product, AddressInterface $address = null);
}
