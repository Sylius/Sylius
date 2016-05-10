<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Factory\CouponFactoryInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, PromotionRepositoryInterface $promotionRepository)
    {
        $this->beConstructedWith($factory, $promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Factory\CouponFactory');
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_coupon_factory_interface()
    {
        $this->shouldImplement(CouponFactoryInterface::class);
    }

    function it_creates_new_coupon(FactoryInterface $factory, CouponInterface $coupon)
    {
        $factory->createNew()->willReturn($coupon);

        $this->createNew()->shouldReturn($coupon);
    }

    function it_throws_an_exception_when_promotion_is_not_found(PromotionRepositoryInterface $promotionRepository)
    {
        $promotionRepository->find(15)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForPromotion', [15])
        ;
    }

    function it_throws_invalid_argument_exception_when_promotion_is_not_coupon_based(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotionRepository->find(13)->willReturn($promotion);
        $promotion->getName()->willReturn('Christmas sale');
        $promotion->isCouponBased()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForPromotion', [13])
        ;
    }

    function it_creates_a_coupon_and_assigns_a_promotion_to_id(
        FactoryInterface $factory,
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion,
        CouponInterface $coupon
    ) {
        $factory->createNew()->willReturn($coupon);
        $promotionRepository->find(13)->willReturn($promotion);
        $promotion->getName()->willReturn('Christmas sale');
        $promotion->isCouponBased()->willReturn(true);
        $coupon->setPromotion($promotion)->shouldBeCalled();

        $this->createForPromotion(13)->shouldReturn($coupon);
    }
}
