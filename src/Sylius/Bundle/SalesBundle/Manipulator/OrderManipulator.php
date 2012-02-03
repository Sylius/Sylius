<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Manipulator;

use Sylius\Bundle\SalesBundle\Model\OrderManagerInterface;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order manipulator.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderManipulator implements OrderManipulatorInterface
{
    protected $orderManager;
    
    public function __construct(OrderManagerInterface $orderManager)
    {
        $this->orderManager = $orderManager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function place(OrderInterface $order)
    {
        $order->incrementCreatedAt();
        $this->orderManager->persistOrder($order);
    }
    
    /**
     * {@inheritdoc}
     */
    public function create(OrderInterface $order)
    {
        $order->incrementCreatedAt();
        $this->orderManager->persistOrder($order);
    }
    
    /**
     * {@inheritdoc}
     */
    public function update(OrderInterface $order)
    {
        $order->incrementUpdatedAt();
        $this->orderManager->persistOrder($order);
    }
    
    /**
     * {@inheritdoc}
     */
    public function delete(OrderInterface $order)
    {
        $this->orderManager->removeOrder($order);
    }
    
    /**
     * {@inheritdoc}
     */
    public function status(OrderInterface $order)
    {
        $this->update($order);
    }

    /**
     * {@inheritdoc}
     */
    public function confirm(OrderInterface $order)
    {
        $order->setConfirmed(true);
        $this->update($order);
    }

    /**
     * {@inheritdoc}
     */
    public function close(OrderInterface $order)
    {
        $order->setClosed(true);
        $this->update($order);
    }

    /**
     * {@inheritdoc}
     */
    public function open(OrderInterface $order)
    {
        $order->setClosed(false);
        $this->update($order);
    }
}
