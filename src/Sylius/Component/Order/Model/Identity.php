<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

/**
 * Identity entity.
 *
 * @author Daniel Kucharski <daniel@xerias.be>
 */
class Identity implements IdentityInterface
{
    /**
     * Id.
     *
     * @var int
     */
    protected $id;

    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Identity name, for instance "ebay id"
     *
     */
    protected $name;

    /**
     * Identity value, for instance "2342343242"
     *
     */
    protected $value;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
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

        return $this;
    }
}
