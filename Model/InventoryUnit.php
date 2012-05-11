<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Model;

/**
 * Inventory unit model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnit implements InventoryUnitInterface
{
    /**
     * Product id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Identifier that tells which stockable this unit represents.
     *
     * @var string
     */
    protected $stockableId;

    /**
     * State of the inventory unit.
     *
     * @var integer
     */
    protected $state;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->state = InventoryUnitInterface::STATE_AVAILABLE;
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
    public function getStockableId()
    {
        return $this->stockableId;
    }

    /**
     * {@inheritdoc}
     */
    public function setStockableId($stockableId)
    {
        $this->stockableId = $stockableId;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
