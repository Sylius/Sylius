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

use Finite\Factory\FactoryInterface;
use Sylius\Bundle\ResourceBundle\Exception\UnexpectedTypeException;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ConfirmOrderListener
{
    protected $finiteFactory;

    public function __construct(FactoryInterface $finiteFactory)
    {
        $this->finiteFactory = $finiteFactory;
    }

    /**
     * Set an Order as completed
     *
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function confirmOrder(GenericEvent $event)
    {
        $order = $this->getOrder($event);

        $this->finiteFactory->get($order, 'sylius_order')->apply('confirm');
    }

    protected function getOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException($order, 'Sylius\Component\Order\Model\OrderInterface');
        }

        return $order;
    }
}
