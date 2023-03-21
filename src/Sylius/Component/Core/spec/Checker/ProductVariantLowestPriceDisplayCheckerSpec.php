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

namespace spec\Sylius\Component\Core\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductVariantLowestPriceDisplayCheckerSpec extends ObjectBehavior
{
    function it_implements_product_variant_lowest_price_checker_interface(): void
    {
        $this->shouldImplement(ProductVariantLowestPriceDisplayCheckerInterface::class);
    }

    function it_returns_false_if_showing_lowest_price_before_discount_is_turned_off_on_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(false);

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(false);
    }

    function it_returns_true_if_the_product_variant_has_no_taxons_assigned(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection());

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(true);
    }

    function it_returns_true_if_there_is_no_taxons_excluded_showing_lowest_price_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $taxon,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(true);
        $channel->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection());

        $productVariant->getProduct()->willReturn($product);
        $product->getTaxons()->willReturn(new ArrayCollection([$taxon->getWrappedObject()]));

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(true);
    }

    function it_returns_false_if_at_least_one_product_variants_taxon_is_excluded_from_showing_lowest_price_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $firstTaxon->getCode()->willReturn('first_taxon');
        $firstTaxon->getChildren()->willReturn(new ArrayCollection());
        $secondTaxon->getCode()->willReturn('second_taxon');
        $secondTaxon->getChildren()->willReturn(new ArrayCollection());

        $product
            ->getTaxons()
            ->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject(), $secondTaxon->getWrappedObject()]))
        ;
        $channel
            ->getTaxonsExcludedFromShowingLowestPrice()
            ->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject()]))
        ;

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(false);
    }

    function it_returns_false_if_parent_of_at_least_one_product_variants_taxon_is_excluded_from_showing_lowest_price_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $firstTaxonChild,
        TaxonInterface $childOfFirstTaxonChild,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $firstTaxon->getCode()->willReturn('first_taxon');
        $firstTaxon->getChildren()->willReturn(new ArrayCollection([$firstTaxonChild->getWrappedObject()]));
        $firstTaxonChild->getCode()->willReturn('first_taxon_child');
        $firstTaxonChild->getChildren()->willReturn(new ArrayCollection([$childOfFirstTaxonChild->getWrappedObject()]));
        $childOfFirstTaxonChild->getCode()->willReturn('child_of_first_taxon_child');
        $childOfFirstTaxonChild->getChildren()->willReturn(new ArrayCollection());
        $secondTaxon->getCode()->willReturn('second_taxon');
        $secondTaxon->getChildren()->willReturn(new ArrayCollection());

        $product
            ->getTaxons()
            ->willReturn(new ArrayCollection([$childOfFirstTaxonChild->getWrappedObject(), $secondTaxon->getWrappedObject()]))
        ;
        $channel
            ->getTaxonsExcludedFromShowingLowestPrice()
            ->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject()]))
        ;

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(false);
    }

    function it_returns_true_if_none_of_the_product_variants_taxons_is_excluded_from_showing_lowest_price_in_channel(
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
    ): void {
        $channel->isLowestPriceForDiscountedProductsVisible()->willReturn(true);
        $productVariant->getProduct()->willReturn($product);

        $firstTaxon->getCode()->willReturn('first_taxon');
        $firstTaxon->getChildren()->willReturn(new ArrayCollection());
        $secondTaxon->getCode()->willReturn('second_taxon');
        $secondTaxon->getChildren()->willReturn(new ArrayCollection());

        $product
            ->getTaxons()
            ->willReturn(new ArrayCollection([$firstTaxon->getWrappedObject(), $secondTaxon->getWrappedObject()]))
        ;
        $channel->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection([]));

        $this->isLowestPriceDisplayable($productVariant, ['channel' => $channel])->shouldReturn(true);
    }

    function it_throws_an_exception_if_there_is_no_channel_passed_in_context(
        ProductVariantInterface $productVariant,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isLowestPriceDisplayable', [$productVariant, []])
        ;
    }

    function it_throws_an_exception_if_there_is_no_channel_set_under_the_channel_key_in_context(
        ProductVariantInterface $productVariant,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isLowestPriceDisplayable', [$productVariant, ['channel' => new \stdClass()]])
        ;
    }
}
