<?php

namespace Sylius\Bundle\PromotionBundle\Tests\Validator;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCouponExpiry;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Validation;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponExpiryValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_coupon_do_not_expire()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(null);

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(null);
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_coupon_is_not_expired()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(null);

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(new \DateTime('tomorrow'));
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_adds_a_violation_if_coupon_is_expired()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(null);

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(new \DateTime('yesterday'));
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(1, $violations);
    }

    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_promotion_do_not_expire()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(null);

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(null);
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_promotion_is_not_expired()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(new \DateTime('tomorrow'));

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(null);
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_adds_a_violation_if_promotion_is_expired()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(new \DateTime('yesterday'));

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(null);
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(1, $violations);
    }

    /**
     * @test
     */
    public function it_adds_only_one_violation_even_if_both_coupon_and_promotion_are_expired()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var PromotionInterface|ObjectProphecy $promotion */
        $promotion = $this->prophesize(PromotionInterface::class);
        $promotion->getEndsAt()->willReturn(new \DateTime('yesterday'));

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);
        $coupon->getExpiresAt()->willReturn(new \DateTime('yesterday'));
        $coupon->getPromotion()->willReturn($promotion->reveal());

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponExpiry());

        $this->assertCount(1, $violations);
    }
}
