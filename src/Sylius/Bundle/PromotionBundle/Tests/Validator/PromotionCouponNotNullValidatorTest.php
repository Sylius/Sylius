<?php

namespace Sylius\Bundle\PromotionBundle\Tests\Validator;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionCouponNotNull;
use Sylius\Component\Promotion\Model\CouponInterface;
use Symfony\Component\Validator\Validation;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PromotionCouponNotNullValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_does_not_add_any_violations_if_coupon_is_passed()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        /** @var CouponInterface|ObjectProphecy $coupon */
        $coupon = $this->prophesize(CouponInterface::class);

        $violations = $validator->validateValue($coupon->reveal(), new PromotionCouponNotNull());

        $this->assertCount(0, $violations);
    }

    /**
     * @test
     */
    public function it_adds_a_violation_if_no_coupon_is_passed()
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        $violations = $validator->validateValue(null, new PromotionCouponNotNull());

        $this->assertCount(1, $violations);
    }
}
