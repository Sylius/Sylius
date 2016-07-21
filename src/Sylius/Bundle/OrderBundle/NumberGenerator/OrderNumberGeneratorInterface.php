<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberGenerator;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OrderNumberGeneratorInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return string
     */
    public function generate(OrderInterface $order);
}
