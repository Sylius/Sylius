<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as VariantInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\Product as SyliusProduct;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductSpec extends ObjectBehavior
{
    function let(VariantInterface $variant)
    {
        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Product');
    }

    function it_implements_Sylius_core_product_interface()
    {
        $this->shouldImplement(ProductInterface::class);
    }

    function it_extends_Sylius_product_model()
    {
        $this->shouldHaveType(SyliusProduct::class);
    }

    function it_has_metadata_class_identifier()
    {
        $this->getMetadataClassIdentifier()->shouldReturn('Product');
    }

    function it_initializes_taxon_collection_by_default()
    {
        $this->getTaxons()->shouldHaveType(Collection::class);
    }

    function its_taxons_are_mutable(Collection $taxons)
    {
        $this->setTaxons($taxons);
        $this->getTaxons()->shouldReturn($taxons);
    }

    function its_variant_selection_method_is_choice_by_default()
    {
        $this->getVariantSelectionMethod()->shouldReturn(Product::VARIANT_SELECTION_CHOICE);
    }

    function its_variant_selection_method_can_be_changed_to_option_match()
    {
        $this->setVariantSelectionMethod(Product::VARIANT_SELECTION_MATCH);
    }

    function it_throws_exception_if_any_other_value_is_given_as_variant_selection_method()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetVariantSelectionMethod('foo')
        ;
    }

    function it_has_no_shipping_category_by_default()
    {
        $this->getShippingCategory()->shouldReturn(null);
    }

    function its_shipping_category_is_mutable(ShippingCategoryInterface $shippingCategory)
    {
        $this->setShippingCategory($shippingCategory);
        $this->getShippingCategory()->shouldReturn($shippingCategory);
    }

    function it_has_no_restricted_zone_by_default()
    {
        $this->getRestrictedZone()->shouldReturn(null);
    }

    function its_restricted_zone_is_mutable(ZoneInterface $zone)
    {
        $this->setRestrictedZone($zone);
        $this->getRestrictedZone()->shouldReturn($zone);
    }

    function it_has_no_main_taxon_by_default()
    {
        $this->getMainTaxon()->shouldReturn(null);
    }

    function it_sets_main_taxon(TaxonInterface $taxon)
    {
        $this->setMainTaxon($taxon);
        $this->getMainTaxon()->shouldReturn($taxon);
    }

    function it_returns_first_variant(VariantInterface $variant)
    {
        $this->addVariant($variant);

        $this->getFirstVariant()->shouldReturn($variant);
    }

    function it_returns_null_as_first_variant_if_product_has_no_variants(VariantInterface $variant)
    {
        $variant->setProduct(null)->shouldBeCalled();
        $this->removeVariant($variant);

        $this->getFirstVariant()->shouldReturn(null);
    }

    function it_returns_first_variants_price_as_product_price(VariantInterface $variant)
    {
        $variant->getPrice()->willReturn(1000);
        $this->addVariant($variant);

        $this->getPrice()->shouldReturn(1000);
    }

    function it_returns_null_as_product_price_if_product_has_no_variants(VariantInterface $variant)
    {
        $variant->setProduct(null)->shouldBeCalled();
        $this->removeVariant($variant);

        $this->getPrice()->shouldReturn(null);
    }

    function it_returns_first_variants_image_as_product_image(
        ImageInterface $image,
        VariantInterface $variant
    ) {
        $variant->getImage()->willReturn($image);
        $this->addVariant($variant);

        $this->getImage()->shouldReturn($image);
    }

    function it_returns_null_as_product_image_if_product_has_no_variants(VariantInterface $variant)
    {
        $variant->setProduct(null)->shouldBeCalled();
        $this->removeVariant($variant);

        $this->getImage()->shouldReturn(null);
    }
}
