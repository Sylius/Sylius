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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface as BaseChannelFactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

interface ChannelFactoryInterface extends BaseChannelFactoryInterface
{
    public function createNamed(string $name): ChannelInterface;
}
