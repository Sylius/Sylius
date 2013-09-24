<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Default zone member zone model.
 *
 * A zone can also consist of other zones.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class ZoneMemberZone extends ZoneMember
{
    /**
     * @var ZoneInterface
     */
    protected $zone;

    /**
     * @return ZoneInterface
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param ZoneInterface $zone
     *
     * @return ZoneMemberZone
     */
    public function setZone(ZoneInterface $zone = null)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->zone->getName();
    }
}
