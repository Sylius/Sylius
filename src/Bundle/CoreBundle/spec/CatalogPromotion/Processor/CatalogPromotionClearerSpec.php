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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionClearerSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_clearer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionClearerInterface::class);
    }

    function it_clears_given_variant_with_catalog_promotions_applied(
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));

        $firstChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection([$catalogPromotion]));
        $firstChannelPricing->getOriginalPrice()->willReturn(1000);
        $firstChannelPricing->setPrice(1000)->shouldBeCalled();
        $firstChannelPricing->clearAppliedPromotions()->shouldBeCalled();

        $secondChannelPricing->getAppliedPromotions()->willReturn(new ArrayCollection());
        $secondChannelPricing->getOriginalPrice()->shouldNotBeCalled();
        $secondChannelPricing->clearAppliedPromotions()->shouldNotBeCalled();

        $this->clearVariant($variant);
    }
}
