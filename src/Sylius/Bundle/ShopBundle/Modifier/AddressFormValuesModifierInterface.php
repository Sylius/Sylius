<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Modifier;

use Sylius\Component\Core\Model\AddressInterface;

interface AddressFormValuesModifierInterface
{
    public function modify(array $newAddress, AddressInterface $address): array;
}
