<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Channel\Factory;

use Sylius\Channel\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface ChannelFactoryInterface extends FactoryInterface
{
    /**
     * @param string $name
     *
     * @return ChannelInterface
     */
    public function createNamed($name);
}
