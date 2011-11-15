<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\EventDispatcher\Event;

use Symfony\Component\EventDispatcher\Event;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Filter order event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FilterOrderEvent extends Event
{
    protected $order;
    
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
}