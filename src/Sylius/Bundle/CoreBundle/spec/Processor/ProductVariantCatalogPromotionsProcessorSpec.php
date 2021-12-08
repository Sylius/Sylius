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

namespace spec\Sylius\Bundle\CoreBundle\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ProductVariantCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator
    ): void {
        $this->beConstructedWith($catalogPromotionClearer, $catalogPromotionApplicator);
    }

    function it_implements_product_catalog_promotions_processor_interface(): void
    {
        $this->shouldImplement(ProductVariantCatalogPromotionsProcessorInterface::class);
    }

    function it_reapplies_catalog_promotion_on_variant(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        CatalogPromotionInterface $catalogPromotion
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));
        $catalogPromotion->isEnabled()->willReturn(true);
        $firstChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
        $secondChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());

        $catalogPromotionClearer->clearChannelPricing($firstChannelPricing)->shouldBeCalled();
        $catalogPromotionClearer->clearChannelPricing($secondChannelPricing)->shouldNotBeCalled();

        $catalogPromotionApplicator->applyOnChannelPricing($firstChannelPricing, $catalogPromotion)->shouldBeCalled();

        $this->process($variant);
    }

    function it_does_nothing_if_channel_pricings_do_not_have_applied_promotions(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionApplicatorInterface $catalogPromotionApplicator,
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));

        $firstChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());
        $secondChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());

        $catalogPromotionClearer->clearChannelPricing($firstChannelPricing)->shouldNotBeCalled();
        $catalogPromotionClearer->clearChannelPricing($secondChannelPricing)->shouldNotBeCalled();

        $catalogPromotionApplicator->applyOnChannelPricing($firstChannelPricing, Argument::any())->shouldNotBeCalled();
        $catalogPromotionApplicator->applyOnChannelPricing($secondChannelPricing, Argument::any())->shouldNotBeCalled();

        $this->process($variant);
    }
}
