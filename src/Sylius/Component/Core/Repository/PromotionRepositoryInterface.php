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

namespace Sylius\Component\Core\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface as BasePromotionRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface PromotionRepositoryInterface extends BasePromotionRepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return PromotionInterface[]
     */
    public function findActiveByChannel(ChannelInterface $channel);
}
