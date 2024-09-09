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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of AddressInterface
 *
 * @extends FactoryInterface<T>
 */
interface AddressFactoryInterface extends FactoryInterface
{
    public function createForCustomer(CustomerInterface $customer): AddressInterface;
}
