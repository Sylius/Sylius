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

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface TestPromotionFactoryInterface
{
    /**
     * @param string $name
     *
     * @return PromotionInterface
     */
    public function create($name);

    /**
     * @param string $name
     * @param ChannelInterface $channel
     *
     * @return PromotionInterface
     */
    public function createForChannel($name, ChannelInterface $channel);
}
