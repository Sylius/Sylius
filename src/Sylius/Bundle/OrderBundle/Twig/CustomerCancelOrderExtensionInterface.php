<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Twig;

use Sylius\Component\Core\Model\OrderInterface;

interface CustomerCancelOrderExtensionInterface
{
    public function canOrderBeCancelled(OrderInterface $order);
}
