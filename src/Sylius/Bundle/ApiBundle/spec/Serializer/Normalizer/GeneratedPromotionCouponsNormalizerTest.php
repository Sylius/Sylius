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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\GeneratedPromotionCouponsNormalizer;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class GeneratedPromotionCouponsNormalizerTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&NormalizerInterface $normalizer;

    private GeneratedPromotionCouponsNormalizer $generatedPromotionCouponsNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);

        $this->generatedPromotionCouponsNormalizer = new GeneratedPromotionCouponsNormalizer(
            $this->sectionProvider,
            ['sylius:promotion_coupon:index'],
        );
        $this->generatedPromotionCouponsNormalizer->setNormalizer($this->normalizer);
    }

    public function testSupportsOnlyArrayCollectionThatContainsCouponsInAdminSectionWithProperData(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        self::assertTrue($this->generatedPromotionCouponsNormalizer
            ->supportsNormalization(new ArrayCollection([$promotionCouponMock]), null, ['groups' => ['sylius:promotion_coupon:index']]))
        ;
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());
        self::assertFalse($this->generatedPromotionCouponsNormalizer
            ->supportsNormalization(new \stdClass(), null, ['groups' => ['sylius:promotion_coupon:index']]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        self::assertFalse($this->generatedPromotionCouponsNormalizer
            ->supportsNormalization(new ArrayCollection([$promotionCouponMock]), null, ['groups' => ['sylius:promotion_coupon:shop']]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->generatedPromotionCouponsNormalizer
            ->supportsNormalization(new ArrayCollection([$promotionCouponMock]), null, ['groups' => ['sylius:promotion_coupon:index']]))
        ;
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->generatedPromotionCouponsNormalizer->supportsNormalization($promotionCouponMock, null, ['groups' => ['sylius:promotion_coupon:index']]));
    }

    public function testDoesNotSupportIfTheNormalizerHasBeenAlreadyCalled(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        self::assertFalse($this->generatedPromotionCouponsNormalizer
            ->supportsNormalization(new ArrayCollection([$promotionCouponMock]), null, [
                'sylius_generated_promotion_coupons_normalizer_already_called' => true,
                'groups' => ['sylius:promotion_coupon:index'],
            ]))
        ;
    }

    public function testCallsDefaultNormalizerWhenGivenResourceIsNotAnInstanceOfArrayCollectionContainingPromotionCouponInterface(): void
    {
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $this->normalizer->expects(self::once())->method('normalize')->with(new ArrayCollection([new \stdClass()]), null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:index'],
        ])
            ->willReturn([])
        ;
        $this->generatedPromotionCouponsNormalizer->normalize(new ArrayCollection([new \stdClass()]), null, ['groups' => ['sylius:promotion_coupon:index']]);
    }

    public function testThrowsAnExceptionIfTheGivenResourceIsNotAnInstanceOfArrayCollection(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->normalizer->expects(self::never())->method('normalize')->with($promotionCouponMock, null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:index'],
        ])
        ;
        $this->expectException(InvalidArgumentException::class);
        $this->generatedPromotionCouponsNormalizer->normalize($promotionCouponMock, null, ['groups' => ['sylius:promotion_coupon:index']]);
    }

    public function testThrowsAnExceptionIfSerializerHasAlreadyBeenCalled(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->normalizer->expects(self::never())->method('normalize')->with(new ArrayCollection([$promotionCouponMock]), null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:index'],
        ])
        ;
        $this->expectException(InvalidArgumentException::class);
        $this->generatedPromotionCouponsNormalizer->normalize(new ArrayCollection([$promotionCouponMock]), null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:index'],
        ]);
    }

    public function testThrowsAnExceptionIfItIsNotAdminSection(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $this->normalizer->expects(self::never())->method('normalize')->with(new ArrayCollection([$promotionCouponMock]), null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:index'],
        ])
        ;
        $this->expectException(InvalidArgumentException::class);
        $this->generatedPromotionCouponsNormalizer->normalize(new ArrayCollection([$promotionCouponMock]), null, ['groups' => ['sylius:promotion_coupon:index']]);
    }

    public function testThrowsAnExceptionIfSerializationGroupIsNotSupported(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());
        $this->normalizer->expects(self::never())->method('normalize')->with(new ArrayCollection([$promotionCouponMock]), null, [
            'sylius_generated_promotion_coupons_normalizer_already_called' => true,
            'groups' => ['sylius:promotion_coupon:show'],
        ])
        ;
        $this->expectException(InvalidArgumentException::class);
        $this->generatedPromotionCouponsNormalizer->normalize(new ArrayCollection([$promotionCouponMock]), null, ['groups' => ['sylius:promotion_coupon:show']]);
    }
}
