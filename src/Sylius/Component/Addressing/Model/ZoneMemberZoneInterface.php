<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * Zone interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ZoneMemberZoneInterface extends ResourceInterface
{
    /**
     * @return ZoneInterface
     */
    public function getZone();

    /**
     * @param ZoneInterface $zone
     */
    public function setZone(ZoneInterface $zone = null);
}
