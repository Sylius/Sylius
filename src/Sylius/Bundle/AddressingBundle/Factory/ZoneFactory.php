<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\Factory;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneFactory extends Factory
{
    /**
     * @param string $type
     *
     * @return ZoneInterface
     */
    public function createTyped($type)
    {
        /* @var ZoneInterface $zone */
        $zone = $this->createNew();
        $zone->setType($type);

        return $zone;
    }
}
