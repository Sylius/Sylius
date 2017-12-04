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

namespace Sylius\Component\Addressing\Comparator;

use Sylius\Component\Addressing\Model\AddressInterface;

interface AddressComparatorInterface
{
    /**
     * @param AddressInterface $firstAddress
     * @param AddressInterface $secondAddress
     *
     * @return bool
     */
    public function equal(AddressInterface $firstAddress, AddressInterface $secondAddress): bool;
}
