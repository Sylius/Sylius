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

namespace spec\Sylius\Bundle\CoreBundle\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicatorSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_applicator_interface(): void
    {
        $this->shouldImplement(CatalogPromotionApplicatorInterface::class);
    }

    function it_applies_percentage_discount_on_all_products_variants(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $catalogPromotion->getActions()->willReturn(new ArrayCollection([
            $catalogPromotionAction->getWrappedObject(),
        ]));

        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $catalogPromotion->getLabel()->willReturn('Winter sale');
        $catalogPromotion->getCode()->willReturn('winter_sale');

        $firstChannelPricing->getPrice()->willReturn(1000);
        $firstChannelPricing->getOriginalPrice()->willReturn(null);
        $firstChannelPricing->setOriginalPrice(1000)->shouldBeCalled();
        $firstChannelPricing->setPrice(700)->shouldBeCalled();
        $firstChannelPricing->addAppliedPromotion(['winter_sale' => ['name' => 'Winter sale']])->shouldBeCalled();

        $secondChannelPricing->getPrice()->willReturn(1400);
        $secondChannelPricing->getOriginalPrice()->willReturn(null);
        $secondChannelPricing->setOriginalPrice(1400)->shouldBeCalled();
        $secondChannelPricing->setPrice(980)->shouldBeCalled();
        $secondChannelPricing->addAppliedPromotion(['winter_sale' => ['name' => 'Winter sale']])->shouldBeCalled();

        $this->applyCatalogPromotion($variant, $catalogPromotion);
    }

    function it_does_not_set_original_price_during_application_if_its_already_there(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelPricingInterface $firstChannelPricing
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
        ]));

        $catalogPromotion->getActions()->willReturn(new ArrayCollection([
            $catalogPromotionAction->getWrappedObject(),
        ]));

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.5]);

        $catalogPromotion->getLabel()->willReturn('Winter sale');
        $catalogPromotion->getCode()->willReturn('winter_sale');

        $firstChannelPricing->getPrice()->willReturn(1000);
        $firstChannelPricing->getOriginalPrice()->willReturn(2000);
        $firstChannelPricing->setOriginalPrice(Argument::any())->shouldNotBeCalled();
        $firstChannelPricing->setPrice(500)->shouldBeCalled();
        $firstChannelPricing->addAppliedPromotion(['winter_sale' => ['name' => 'Winter sale']])->shouldBeCalled();

        $this->applyCatalogPromotion($variant, $catalogPromotion);
    }
}
