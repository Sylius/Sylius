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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GeneratedPromotionCouponsNormalizerSpec extends ObjectBehavior
{
    public function let(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($sectionProvider, ['sylius:promotion_coupon:index']);
        $this->setNormalizer($normalizer);
    }

    public function it_supports_only_array_collection_that_contains_coupons_in_admin_section_with_proper_data(
        SectionProviderInterface $sectionProvider,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this
            ->supportsNormalization(new ArrayCollection([$promotionCoupon]), null, ['groups' => ['sylius:promotion_coupon:index']])
            ->shouldReturn(true)
        ;

        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this
            ->supportsNormalization(new \stdClass(), null, ['groups' => ['sylius:promotion_coupon:index']])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this
            ->supportsNormalization(new ArrayCollection([$promotionCoupon]), null, ['groups' => ['sylius:promotion_coupon:shop']])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization(new ArrayCollection([$promotionCoupon]), null, ['groups' => ['sylius:promotion_coupon:index']])
            ->shouldReturn(false)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this->supportsNormalization($promotionCoupon, null, ['groups' => ['sylius:promotion_coupon:index']])->shouldReturn(false);
    }

    public function it_does_not_support_if_the_normalizer_has_been_already_called(
        SectionProviderInterface $sectionProvider,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsNormalization(new ArrayCollection([$promotionCoupon]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ])
            ->shouldReturn(false)
        ;
    }

    public function it_calls_default_normalizer_when_given_resource_is_not_an_instance_of_array_collection_containing_promotion_coupon_interface(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $normalizer
            ->normalize(new ArrayCollection([new \stdClass()]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ])
            ->willReturn([])
            ->shouldBeCalled()
        ;

        $this->normalize(new ArrayCollection([new \stdClass()]), null, ['groups' => ['sylius:promotion_coupon:index']]);
    }

    public function it_throws_an_exception_if_the_given_resource_is_not_an_instance_of_array_collection(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();
        $normalizer
            ->normalize($promotionCoupon, null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [$promotionCoupon, null, ['groups' => ['sylius:promotion_coupon:index']]])
        ;
    }

    public function it_throws_an_exception_if_serializer_has_already_been_called(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();
        $normalizer
            ->normalize(new ArrayCollection([$promotionCoupon]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [new ArrayCollection([$promotionCoupon]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ]])
        ;
    }

    public function it_throws_an_exception_if_it_is_not_admin_section(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $normalizer
            ->normalize(new ArrayCollection([$promotionCoupon]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [new ArrayCollection([$promotionCoupon]), null, ['groups' => ['sylius:promotion_coupon:index']]])
        ;
    }

    public function it_throws_an_exception_if_serialization_group_is_not_supported(
        SectionProviderInterface $sectionProvider,
        NormalizerInterface $normalizer,
        PromotionCouponInterface $promotionCoupon,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $normalizer
            ->normalize(new ArrayCollection([$promotionCoupon]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:show'],
            ])
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('normalize', [new ArrayCollection([$promotionCoupon]), null, ['groups' => ['sylius:promotion_coupon:show']]])
        ;
    }
}
