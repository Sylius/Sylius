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

namespace Sylius\Component\Addressing\Matcher;

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

interface ZoneMatcherInterface
{
    /**
     * Returns the best matching zone for given address.
     */
    public function match(AddressInterface $address, ?string $scope = null): ?ZoneInterface;

    /**
     * Returns all matching zones for given address.
     *
     *
     * @return array|ZoneInterface[]
     */
    public function matchAll(AddressInterface $address, ?string $scope = null): array;
}
