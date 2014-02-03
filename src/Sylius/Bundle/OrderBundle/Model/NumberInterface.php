<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Model;

interface NumberInterface
{
    public function getId();
    public function getOrder();
    public function setOrder(OrderInterface $order);
}
