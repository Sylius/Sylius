<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

/**
 * Model for order line items.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Item implements ItemInterface
{
    /**
     * Item id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Quantity.
     *
     * @var integer
     */
    protected $quantity;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;
    }
}
