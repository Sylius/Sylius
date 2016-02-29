<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Checker;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\RestrictedZoneInterface;

interface RestrictedZoneCheckerInterface
{
    /**
     * @param object           $subject
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function isRestricted(RestrictedZoneInterface $subject, AddressInterface $address = null);
}
