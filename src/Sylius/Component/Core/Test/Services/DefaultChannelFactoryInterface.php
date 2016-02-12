<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface DefaultChannelFactoryInterface
{
    /**
     * @return ChannelRepositoryInterface
     */
    public function create();
}
