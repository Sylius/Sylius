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

namespace Sylius\Bundle\ShopBundle\Modifier;

use Sylius\Component\Addressing\Model\AddressInterface;

interface AddressFormValuesModifierInterface
{
    /**
     * @param array<string, mixed> $newAddress
     * @return array<string, mixed>
     */
    public function modify(array $newAddress, AddressInterface $address): array;
}
