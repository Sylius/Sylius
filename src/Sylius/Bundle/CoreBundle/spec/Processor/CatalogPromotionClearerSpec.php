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
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ChannelPricingRepositoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTransitions;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class CatalogPromotionClearerSpec extends ObjectBehavior
{
    function let(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        FactoryInterface $stateMachine
    ): void {
        $this->beConstructedWith($channelPricingRepository, $catalogPromotionRepository, $stateMachine);
    }

    function it_implements_catalog_promotion_clearer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionClearerInterface::class);
    }

    function it_clears_channel_pricings_with_catalog_promotions_applied(
        ChannelPricingRepositoryInterface $channelPricingRepository,
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        FactoryInterface $stateMachine,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        CatalogPromotionInterface $catalogPromotion,
        StateMachineInterface $stateMachineInterface
    ): void {
        $channelPricingRepository->findWithDiscountedPrice()->willReturn([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]);

        $firstChannelPricing->getAppliedPromotions()->willReturn(['winter_sale' => ['en_US' => ['name' => 'Winter sale']]]);
        $firstChannelPricing->getOriginalPrice()->willReturn(1000);
        $firstChannelPricing->setPrice(1000)->shouldBeCalled();
        $firstChannelPricing->clearAppliedPromotions()->shouldBeCalled();

        $secondChannelPricing->getAppliedPromotions()->willReturn([]);
        $secondChannelPricing->getOriginalPrice()->shouldNotBeCalled();
        $secondChannelPricing->clearAppliedPromotions()->shouldNotBeCalled();

        $catalogPromotionRepository->findByCodes(['winter_sale'])->willReturn([$catalogPromotion]);

        $stateMachine->get($catalogPromotion, CatalogPromotionTransitions::GRAPH)->willReturn($stateMachineInterface);
        $stateMachineInterface->apply(CatalogPromotionTransitions::TRANSITION_DEACTIVATE)->shouldBeCalled();

        $this->clear();
    }

    function it_clears_given_variant_with_catalog_promotions_applied(
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));

        $firstChannelPricing->getAppliedPromotions()->willReturn(['winter_sale' => ['en_US' => ['name' => 'Winter sale']]]);
        $firstChannelPricing->getOriginalPrice()->willReturn(1000);
        $firstChannelPricing->setPrice(1000)->shouldBeCalled();
        $firstChannelPricing->clearAppliedPromotions()->shouldBeCalled();

        $secondChannelPricing->getAppliedPromotions()->willReturn([]);
        $secondChannelPricing->getOriginalPrice()->shouldNotBeCalled();
        $secondChannelPricing->clearAppliedPromotions()->shouldNotBeCalled();

        $this->clearVariant($variant);
    }

    function it_clears_given_channel_pricing_with_catalog_promotions_applied(
        ChannelPricingInterface $channelPricing
    ): void {
        $channelPricing->getAppliedPromotions()->willReturn(['winter_sale' => ['en_US' => ['name' => 'Winter sale']]]);
        $channelPricing->getOriginalPrice()->willReturn(1000);
        $channelPricing->setPrice(1000)->shouldBeCalled();
        $channelPricing->clearAppliedPromotions()->shouldBeCalled();

        $this->clearChannelPricing($channelPricing);
    }

    function it_does_not_copy_update_price_when_original_price_is_null(
        ChannelPricingInterface $channelPricing
    ): void {
        $channelPricing->getAppliedPromotions()->willReturn(['winter_sale' => ['en_US' => ['name' => 'Winter sale']]]);
        $channelPricing->getOriginalPrice()->willReturn(null);
        $channelPricing->setPrice(Argument::any())->shouldNotBeCalled();
        $channelPricing->clearAppliedPromotions()->shouldBeCalled();

        $this->clearChannelPricing($channelPricing);
    }
}
