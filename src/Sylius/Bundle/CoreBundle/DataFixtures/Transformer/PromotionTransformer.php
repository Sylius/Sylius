<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Transformer;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionActionFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PromotionTransformer implements PromotionTransformerInterface
{
    use TransformNameToCodeAttributeTrait;
    use TransformChannelsAttributeTrait;
    use TransformPromotionActionsAttributeTrait;
    use TransformPromotionRulesAttributeTrait;

    public function __construct(
        private PromotionActionFactoryInterface $promotionActionFactory,
        private PromotionRuleFactoryInterface $promotionRuleFactory,
        private FactoryInterface $couponFactory,
        private ChannelFactoryInterface $channelFactory,
    ) {
    }

    public function transform(array $attributes): array
    {
        $attributes = $this->transformNameToCodeAttribute($attributes);
        $attributes = $this->transformActionsAttribute($attributes);
        $attributes = $this->transformRulesAttribute($attributes);
        $attributes = $this->transformChannelsAttribute($attributes);

        return $this->transformCouponsAttributes($attributes);
    }

    private function transformCouponsAttributes(array $attributes): array
    {
        $coupons = [];
        foreach ($attributes['coupons'] as $couponDefinition) {
            /** @var PromotionCouponInterface $coupon */
            $coupon = $this->couponFactory->createNew();
            $coupon->setCode($couponDefinition['code']);
            $coupon->setPerCustomerUsageLimit($couponDefinition['per_customer_usage_limit']);
            $coupon->setReusableFromCancelledOrders($couponDefinition['reusable_from_cancelled_orders'] ?? true);
            $coupon->setUsageLimit($couponDefinition['usage_limit']);

            if (null !== $couponDefinition['expires_at']) {
                $coupon->setExpiresAt(new \DateTime($couponDefinition['expires_at']));
            }

            $coupons[] = $coupon;
        }

        $attributes['coupons'] = $coupons;

        return $attributes;
    }
}
