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
 * @template T of PromotionInterface
 *
 * @extends BasePromotionRepositoryInterface<T>
 */
interface PromotionRepositoryInterface extends BasePromotionRepositoryInterface
{
    /**
     * @return array|PromotionInterface[]
     */
    public function findActiveByChannel(ChannelInterface $channel): array;

    /**
     * @return array|PromotionInterface[]
     */
    public function findActiveNonCouponBasedByChannel(ChannelInterface $channel): array;
}
