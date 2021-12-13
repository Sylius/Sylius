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
use Sylius\Bundle\CoreBundle\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\Formatter\AppliedPromotionInformationFormatterInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicatorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $this->beConstructedWith($priceCalculator);
    }

    function it_implements_catalog_promotion_applicator_interface(): void
    {
        $this->shouldImplement(CatalogPromotionApplicatorInterface::class);
    }

    function it_applies_percentage_discount_on_product_variant(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $catalogPromotion->isExclusive()->willReturn(false);
        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));
        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $variant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $variant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);

        $firstChannelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $firstChannelPricing->getPrice()->willReturn(1000);
        $firstChannelPricing->getOriginalPrice()->willReturn(null);
        $firstChannelPricing->getMinimumPrice()->willReturn(0);
        $firstChannelPricing->setOriginalPrice(1000)->shouldBeCalled();
        $firstChannelPricing->setPrice(700)->shouldBeCalled();
        $firstChannelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $priceCalculator->calculate($firstChannelPricing, $catalogPromotionAction)->willReturn(700);

        $secondChannelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $secondChannelPricing->getPrice()->willReturn(1400);
        $secondChannelPricing->getOriginalPrice()->willReturn(null);
        $secondChannelPricing->getMinimumPrice()->willReturn(0);
        $secondChannelPricing->setOriginalPrice(1400)->shouldBeCalled();
        $secondChannelPricing->setPrice(980)->shouldBeCalled();
        $secondChannelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $priceCalculator->calculate($secondChannelPricing, $catalogPromotionAction)->willReturn(980);

        $this->applyOnVariant($variant, $catalogPromotion);
    }

    function it_applies_discount_on_product_variant_only_if_exclusive_promotion_is_not_already_applied(
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));

        $variant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $variant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);

        $firstChannelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(true);

        $firstChannelPricing->addAppliedPromotion($catalogPromotion)->shouldNotBeCalled();

        $secondChannelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $secondChannelPricing->getPrice()->willReturn(1400);
        $secondChannelPricing->getOriginalPrice()->willReturn(null);
        $secondChannelPricing->getMinimumPrice()->willReturn(0);
        $secondChannelPricing->setOriginalPrice(1400)->shouldBeCalled();

        $secondChannelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $priceCalculator->calculate($secondChannelPricing, $catalogPromotionAction)->willReturn(980);

        $secondChannelPricing->setPrice(980)->shouldBeCalled();
        $secondChannelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $catalogPromotion->isExclusive()->willReturn(false);

        $this->applyOnVariant($variant, $catalogPromotion);
    }

    function it_does_not_set_original_price_during_application_if_its_already_there(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        ChannelInterface $channel,
        ChannelPricingInterface $channelPricing
    ): void {
        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $variant->getChannelPricingForChannel($channel)->willReturn($channelPricing);

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.5]);

        $channelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getOriginalPrice()->willReturn(2000);
        $channelPricing->getMinimumPrice()->willReturn(0);
        $channelPricing->setOriginalPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->setPrice(500)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $priceCalculator->calculate($channelPricing, $catalogPromotionAction)->willReturn(500);

        $this->applyOnVariant($variant, $catalogPromotion);
    }

    function it_applies_percentage_discount_on_channel_pricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        ChannelPricingInterface $channelPricing,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB');

        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getMinimumPrice()->willReturn(0);
        $channelPricing->getOriginalPrice()->willReturn(null);
        $channelPricing->getChannelCode()->willReturn('WEB');
        $channelPricing->setOriginalPrice(1000)->shouldBeCalled();
        $channelPricing->setPrice(700)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();
        $priceCalculator->calculate($channelPricing, $catalogPromotionAction)->willReturn(700);

        $this->applyOnChannelPricing($channelPricing, $catalogPromotion);
    }

    function it_does_not_apply_percentage_discount_on_channel_pricing_if_catalog_promotion_does_not_have_the_proper_channel(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelPricingInterface $channelPricing,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB');

        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $channelPricing->getChannelCode()->willReturn('MOBILE');

        $channelPricing->setOriginalPrice(1000)->shouldNotBeCalled();
        $channelPricing->setPrice(700)->shouldNotBeCalled();
        $channelPricing->addAppliedPromotion(Argument::any())->shouldNotBeCalled();

        $this->applyOnChannelPricing($channelPricing, $catalogPromotion);
    }

    function it_does_not_apply_catalog_promotion_below_minimum_price(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        ChannelPricingInterface $channelPricing,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB');

        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $channelPricing->getPrice()->willReturn(1000);
        $channelPricing->getMinimumPrice()->willReturn(900);
        $channelPricing->getOriginalPrice()->willReturn(null);
        $channelPricing->getChannelCode()->willReturn('WEB');
        $priceCalculator->calculate($channelPricing, $catalogPromotionAction)->willReturn(900);

        $channelPricing->setOriginalPrice(1000)->shouldBeCalled();
        $channelPricing->setPrice(900)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyOnChannelPricing($channelPricing, $catalogPromotion);
    }

    function it_does_not_apply_catalog_promotion_if_product_variant_is_at_its_minimum_price(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelPricingInterface $channelPricing,
        ChannelInterface $channel
    ): void {
        $channel->getCode()->willReturn('WEB');

        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $channelPricing->hasExclusiveCatalogPromotionApplied()->willReturn(false);
        $channelPricing->getPrice()->willReturn(900);
        $channelPricing->getMinimumPrice()->willReturn(900);
        $channelPricing->getOriginalPrice()->willReturn(900);
        $channelPricing->getChannelCode()->willReturn('WEB');

        $channelPricing->setOriginalPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->setPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->addAppliedPromotion(Argument::any())->shouldNotBeCalled();

        $this->applyOnChannelPricing($channelPricing, $catalogPromotion);
    }
}
