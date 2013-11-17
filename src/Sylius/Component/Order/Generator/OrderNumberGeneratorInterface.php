<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Generator;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Order number generator interface.
 * The implementation should generate next order number.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderNumberGeneratorInterface
{
    /**
     * Generate next available order number.
     *
     * @param OrderInterface $order
     */
    public function generate(OrderInterface $order);
}
