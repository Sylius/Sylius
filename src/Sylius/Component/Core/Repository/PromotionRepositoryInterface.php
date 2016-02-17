<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface as BasePromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PromotionRepositoryInterface extends BasePromotionInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return mixed
     */
    public function findActiveByChannel(ChannelInterface $channel);
}
