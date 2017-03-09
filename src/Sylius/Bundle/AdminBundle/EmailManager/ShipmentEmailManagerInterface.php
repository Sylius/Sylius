<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\EmailManager;

use Sylius\Component\Core\Model\ShipmentInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ShipmentEmailManagerInterface
{
    /**
     * @param ShipmentInterface $shipment
     */
    public function sendConfirmationEmail(ShipmentInterface $shipment);
}
