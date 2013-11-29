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

use Sylius\Component\Taxation\Model\TaxRateInterface as BaseTaxRateInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * Tax rate interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxRateInterface extends BaseTaxRateInterface
{
    /**
     * Get zone.
     *
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * Set zone.
     *
     * @param ZoneInterface $zone
     */
    public function setZone(ZoneInterface $zone);
}
