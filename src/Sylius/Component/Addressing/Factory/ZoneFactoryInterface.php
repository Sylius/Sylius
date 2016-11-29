<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface ZoneFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     *
     * @return ZoneInterface
     */
    public function createTyped($type);

    /**
     * @param array $membersCodes
     *
     * @return ZoneInterface
     */
    public function createWithMembers(array $membersCodes);
}
