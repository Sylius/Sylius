<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Model;

/**
 * Transfer of stock from a source location to the destination location
 *
 * @author Patrick Berenschot <p.berenschot@take-abyte.eu>
 */
class StockTransfer implements StockTransferInterface
{
    /**
     * Transfer id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Source location
     *
     * @var StockLocationInterface
     */
    protected $source;

    /**
     * Destination location
     *
     * @var StockLocationInterface
     */
    protected $destination;

    /**
     * Get the id for the stock transfer
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function setDestination(StockLocationInterface $destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function setSource(StockLocationInterface $source)
    {
        $this->source = $source;

        return $this;
    }
}
