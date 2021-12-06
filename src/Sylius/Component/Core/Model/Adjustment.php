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

use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;

class Adjustment extends BaseAdjustment implements AdjustmentInterface
{
    /**
     * @var ShipmentInterface|null
     */
    protected $shipment;

    public function getShipment(): ?ShipmentInterface
    {
        return $this->shipment;
    }

    public function setShipment(?ShipmentInterface $shipment): void
    {
        if ($this->shipment === $shipment) {
            return;
        }

        if ($this->shipment !== null) {
            $this->shipment->removeAdjustment($this);
        }

        $this->shipment = $shipment;

        if ($shipment !== null) {
            $this->setAdjustable($this->shipment->getOrder());
        }
    }
}
