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
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionApplicatorSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_applicator_interface(): void
    {
        $this->shouldImplement(CatalogPromotionApplicatorInterface::class);
    }

    function it_applies_percentage_discount_on_all_products_variants(
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
            $secondChannelPricing->getWrappedObject(),
        ]));

        $firstChannelPricing->getPrice()->willReturn(1000);
        $firstChannelPricing->getOriginalPrice()->willReturn(null);
        $firstChannelPricing->setOriginalPrice(1000)->shouldBeCalled();
        $firstChannelPricing->setPrice(600)->shouldBeCalled();

        $secondChannelPricing->getPrice()->willReturn(1400);
        $secondChannelPricing->getOriginalPrice()->willReturn(null);
        $secondChannelPricing->setOriginalPrice(1400)->shouldBeCalled();
        $secondChannelPricing->setPrice(840)->shouldBeCalled();

        $this->applyPercentageDiscount($variant, 0.4);
    }

    function it_does_not_set_original_price_during_application_if_its_already_there(
        ProductVariantInterface $variant,
        ChannelPricingInterface $firstChannelPricing
    ): void {
        $variant->getChannelPricings()->willReturn(new ArrayCollection([
            $firstChannelPricing->getWrappedObject(),
        ]));

        $firstChannelPricing->getPrice()->willReturn(1000);
        $firstChannelPricing->getOriginalPrice()->willReturn(2000);
        $firstChannelPricing->setOriginalPrice(Argument::any())->shouldNotBeCalled();
        $firstChannelPricing->setPrice(500)->shouldBeCalled();

        $this->applyPercentageDiscount($variant, 0.5);
    }
}
