<?php

namespace Sylius\Bundle\PromotionBundle\Tests\Validator;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCouponUsageLimit;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Validator\Validation;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponUsageLimitValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_coupon_usage_limit_is_not_set()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getUsageLimit()->willReturn(null);

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponUsageLimit());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_coupon_usage_limit_is_not_reached()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getUsageLimit()->willReturn(10);
        $coupon->getUsed()->willReturn(9);

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponUsageLimit());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_adds_a_violation_if_coupon_usage_limit_is_reached()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getUsageLimit()->willReturn(10);
        $coupon->getUsed()->willReturn(10);

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponUsageLimit());

        $this->assertCount(1, $violations);
    }
}
