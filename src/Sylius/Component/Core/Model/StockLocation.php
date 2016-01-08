<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Inventory\Model\StockLocation as BaseStockLocation;

/**
 * Stock location with an address.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocation extends BaseStockLocation implements StockLocationInterface
{
    /**
     * Address instance.
     *
     * @var AddressInterface
     */
    protected $address;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress(AddressInterface $address = null)
    {
        $this->address = $address;
    }
}
