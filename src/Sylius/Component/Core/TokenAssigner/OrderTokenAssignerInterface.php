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

namespace Sylius\Component\Core\TokenAssigner;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface OrderTokenAssignerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function assignTokenValue(OrderInterface $order): void;
}
