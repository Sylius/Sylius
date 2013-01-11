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
 * Stockable model.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class Stockable implements StockableInterface
{
    /**
     * Stockable id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Stockable SKU.
     *
     * @var string
     */
    protected $sku;

    /**
     * Inventory displayed name.
     *
     * @var string
     */
    protected $inventoryName;

    /**
     * Current stock level.
     *
     * @var int
     */
    protected $onHand;

    /**
     * Is stock available on demand?
     *
     * @var Boolean
     */
    protected $availableOnDemand;

    public function __construct()
    {
        $this->onHand = 1;
        $this->availableOnDemand = true;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->inventoryName;
    }

    public function setInventoryName($inventoryName)
    {
        $this->inventoryName = $inventoryName;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock()
    {
        return 0 < $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableOnDemand()
    {
        return $this->availableOnDemand;
    }

    public function setAvailableOnDemand($availableOnDemand)
    {
        $this->availableOnDemand = (Boolean) $availableOnDemand;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnHand()
    {
        return $this->onHand;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnHand($onHand)
    {
        $this->onHand = $onHand;
    }
}
