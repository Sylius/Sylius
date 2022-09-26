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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Factory\Updater;

use Sylius\Component\Core\Model\PromotionInterface;

final class PromotionFactoryUpdater implements PromotionFactoryUpdaterInterface
{
    public function update(PromotionInterface $promotion, array $attributes): void
    {
        $promotion->setCode($attributes['code']);
        $promotion->setName($attributes['name']);
        $promotion->setDescription($attributes['description']);
        $promotion->setUsageLimit($attributes['usage_limit']);
        $promotion->setCouponBased($attributes['coupon_based']);
        $promotion->setExclusive($attributes['exclusive']);
        $promotion->setPriority($attributes['priority']);
        $promotion->setStartsAt($attributes['starts_at']);
        $promotion->setEndsAt($attributes['ends_at']);

        foreach ($attributes['channels'] as $channel) {
            $promotion->addChannel($channel);
        }

        foreach ($attributes['rules'] as $rule) {
            $promotion->addRule($rule);
        }

        foreach ($attributes['actions'] as $action) {
            $promotion->addAction($action);
        }

        foreach ($attributes['coupons'] as $coupon) {
            $promotion->addCoupon($coupon);
        }
    }
}
