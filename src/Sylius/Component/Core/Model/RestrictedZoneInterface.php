<?php

namespace Sylius\Component\Core\Model;

use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @author   vidy   <videni@foxmail.com>
 */

interface RestrictedZoneInterface
{
    /**
     * @return ZoneInterface
     */
    public function getRestrictedZone();

    /**
     * @param ZoneInterface $zone
     */
    public function setRestrictedZone(ZoneInterface $zone = null);
}