<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Shipping\Model\Shipment as BaseShipment;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Shipment extends BaseShipment implements ShipmentInterface
{
    /**
     * @var BaseOrderInterface
     */
    protected $order;

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
    public function setOrder(BaseOrderInterface $order = null)
    {
        $this->order = $order;
    }
}
