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

namespace Sylius\Component\Core\Customer;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface OrderAddressesSaverInterface
{
    /**
     * @param OrderInterface $order
     */
    public function saveAddresses(OrderInterface $order): void;
}
