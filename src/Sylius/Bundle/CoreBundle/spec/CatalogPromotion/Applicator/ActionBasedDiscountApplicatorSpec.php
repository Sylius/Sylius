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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class ActionBasedDiscountApplicatorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
    ): void {
        $this->beConstructedWith($priceCalculator, [$minimumPriceCriteria, $exclusiveCriteria]);
    }

    function it_implements_action_based_discount_applicator_interface(): void
    {
        $this->shouldImplement(ActionBasedDiscountApplicatorInterface::class);
    }

    function it_applies_discount_if_all_criteria_are_valid(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->shouldBeCalled();
        $channelPricing->getOriginalPrice()->willReturn(300);

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_does_not_apply_discount_if_atleast_one_criteria_is_invalid(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(false);

        $channelPricing->getOriginalPrice()->willReturn(300);

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->addAppliedPromotion(Argument::any())->shouldNotBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_does_not_set_original_price_during_application_if_its_already_there(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->shouldBeCalled();

        $channelPricing->getOriginalPrice()->willReturn(200);

        $channelPricing->getPrice()->shouldNotBeCalled();

        $channelPricing->setOriginalPrice(Argument::any())->shouldNotBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_sets_original_price_on_channel_pricing_if_original_price_is_not_set(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->shouldBeCalled();

        $channelPricing->getOriginalPrice()->willReturn(null);

        $channelPricing->getPrice()->willReturn(200);

        $channelPricing->setOriginalPrice(200)->shouldBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_does_not_apply_discount_if_price_calculator_throws_exception(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(false);

        $channelPricing->getOriginalPrice()->shouldNotBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willThrow(ActionBasedPriceCalculatorNotFoundException::class);

        $channelPricing->setPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->addAppliedPromotion(Argument::any())->shouldNotBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_sets_price_as_original_price_when_there_are_no_applied_promotions_and_original_price_is_specified(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());
        $channelPricing->getOriginalPrice()->willReturn(300);

        $channelPricing->setPrice(300)->shouldBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_does_not_set_price_as_original_price_when_there_are_applied_promotions_and_original_price_is_specified(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->willReturn(new ArrayCollection([$catalogPromotion->getWrappedObject()]));
        $channelPricing->getOriginalPrice()->willReturn(300);

        $channelPricing->setPrice(300)->shouldNotBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    function it_does_not_set_price_as_original_price_when_there_are_no_applied_promotions_and_original_price_is_not_specified(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
        DiscountApplicationCriteriaInterface $minimumPriceCriteria,
        DiscountApplicationCriteriaInterface $exclusiveCriteria,
        CatalogPromotionPriceCalculatorInterface $priceCalculator,
    ): void {
        $minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);
        $exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])->willReturn(true);

        $channelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());
        $channelPricing->getOriginalPrice()->willReturn(null);

        $channelPricing->setPrice(300)->shouldNotBeCalled();

        $channelPricing->getPrice()->willReturn(200);
        $channelPricing->setOriginalPrice(200)->shouldBeCalled();

        $priceCalculator->calculate($channelPricing, $action)->willReturn(100);

        $channelPricing->setPrice(100)->shouldBeCalled();
        $channelPricing->addAppliedPromotion($catalogPromotion)->shouldBeCalled();

        $this->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }
}
