<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Mailer\OrderUpdateMailerInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\EventDispatcher\Event\OrderUpdateEvent;

/**
 * Sends Order Update email when triggered by event
 *
 * @author Myke Hines <myke@webhines.com>
 */
class OrderUpdateEmailListener
{
    /**
     * @var OrderUpdateMailerInterface
     */
    protected $mailer;

    public function __construct(OrderUpdateMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param  OrderUpdateEvent          $event
     * @throws \InvalidArgumentException
     */
    public function processOrderUpdate(OrderUpdateEvent $event)
    {
        $order = $event->getOrder();
        $history = $event->getHistory();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order update email listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $this->mailer->sendOrderUpdate($order, $history);
    }
}
