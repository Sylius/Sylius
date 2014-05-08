<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventListener;

use Sylius\Bundle\SequenceBundle\Doctrine\ORM\NumberListener;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets appropriate order number before saving.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberListener
{
    /**
     * Order number listener
     *
     * @var NumberListener
     */
    protected $listener;

    /**
     * Constructor.
     *
     * @param NumberListener $listener
     */
    public function __construct(NumberListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Use generator to add a proper number to order.
     *
     * @param GenericEvent $event
     */
    public function generateOrderNumber(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (null !== $order->getNumber()) {
            return;
        }

        $this->listener->enableEntity($order);
    }
}
