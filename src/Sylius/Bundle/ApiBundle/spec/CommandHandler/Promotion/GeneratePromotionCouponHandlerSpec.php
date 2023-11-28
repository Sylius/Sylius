<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Promotion;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Promotion\GeneratePromotionCoupon;
use Sylius\Bundle\ApiBundle\Command\Promotion\PromotionCouponGeneratorInstruction;
use Sylius\Bundle\ApiBundle\Exception\PromotionNotFoundException;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;

final class GeneratePromotionCouponHandlerSpec extends ObjectBehavior
{
    function let(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponGeneratorInterface $promotionCouponGenerator,
    ) {
        $this->beConstructedWith(
            $promotionRepository,
            $promotionCouponGenerator,
        );
    }

    function it_throws_exception_if_promotion_is_not_found(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponGeneratorInstruction $promotionCouponGeneratorInstruction
    ): void {
        $promotionRepository->findOneBy(['code' => 'promotion_code'])->willReturn(null);

        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code', $promotionCouponGeneratorInstruction->getWrappedObject());

        $this->shouldThrow(PromotionNotFoundException::class)
            ->during('__invoke', [$generatePromotionCoupon])
        ;
    }

    function it_generates_promotion_coupons(
        PromotionRepositoryInterface $promotionRepository,
        PromotionCouponGeneratorInterface $promotionCouponGenerator,
        PromotionInterface $promotion,
        PromotionCouponGeneratorInstruction $promotionCouponGeneratorInstruction,
        PromotionCouponInterface $promotionCouponOne,
        PromotionCouponInterface $promotionCouponTwo
    ): void {
        $promotionRepository->findOneBy(['code' => 'promotion_code'])->willReturn($promotion);

        $generatePromotionCoupon = new GeneratePromotionCoupon('promotion_code', $promotionCouponGeneratorInstruction->getWrappedObject());

        $promotionCouponGenerator->generate($promotion, $promotionCouponGeneratorInstruction)->willReturn([$promotionCouponOne, $promotionCouponTwo]);

        $this($generatePromotionCoupon)->shouldIterateAs([$promotionCouponOne, $promotionCouponTwo]);
    }
}
