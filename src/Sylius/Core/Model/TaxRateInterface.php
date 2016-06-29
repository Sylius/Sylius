<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Taxation\Model\TaxRateInterface as BaseTaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxRateInterface extends BaseTaxRateInterface
{
    /**
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface $zone
     */
    public function setZone(ZoneInterface $zone);
}
