<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventDispatcher\Event;

use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Model\HistoryInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Order update event.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class OrderUpdateEvent extends Event
{
    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Order history.
     *
     * @var HistoryInterface
     */
    protected $history;

    /**
     * Constructor.
     *
     * @param OrderInterface $order
     */
    public function __construct(OrderInterface $order, HistoryInterface $history = null)
    {
        $this->order = $order;
        $this->history = $history;
    }

    /**
     * Get order.
     *
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get history.
     *
     * @return HistoryInterface
     */
    public function getHistory()
    {
        return $this->history;
    }
}
