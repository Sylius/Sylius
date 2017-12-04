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

namespace spec\Sylius\Component\Promotion\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Factory\PromotionCouponFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PromotionCouponFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_a_coupon_factory_interface(): void
    {
        $this->shouldImplement(PromotionCouponFactoryInterface::class);
    }

    function it_creates_a_new_coupon(FactoryInterface $factory, PromotionCouponInterface $coupon): void
    {
        $factory->createNew()->willReturn($coupon);

        $this->createNew()->shouldReturn($coupon);
    }

    function it_throws_an_invalid_argument_exception_when_promotion_is_not_coupon_based(
        PromotionInterface $promotion
    ): void {
        $promotion->getName()->willReturn('Christmas sale');
        $promotion->isCouponBased()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForPromotion', [$promotion])
        ;
    }

    function it_creates_a_coupon_and_assigns_a_promotion_to_id(
        FactoryInterface $factory,
        PromotionInterface $promotion,
        PromotionCouponInterface $coupon
    ): void {
        $factory->createNew()->willReturn($coupon);
        $promotion->getName()->willReturn('Christmas sale');
        $promotion->isCouponBased()->willReturn(true);
        $coupon->setPromotion($promotion)->shouldBeCalled();

        $this->createForPromotion($promotion)->shouldReturn($coupon);
    }
}
