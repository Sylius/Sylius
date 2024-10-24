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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of CustomerInterface
 *
 * @extends FactoryInterface<T>
 */
interface CustomerAfterCheckoutFactoryInterface extends FactoryInterface
{
    /**
     * @return T
     */
    public function createAfterCheckout(OrderInterface $order): CustomerInterface;
}
