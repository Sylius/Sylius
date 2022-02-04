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

namespace Sylius\Bundle\AddressingBundle\Checker;

use Sylius\Component\Addressing\Model\AddressInterface;

interface ProvinceAddressCheckerInterface
{
    public function isValid(AddressInterface $address): bool;
}
