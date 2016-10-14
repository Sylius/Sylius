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
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as VariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * @mixin Product
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ProductSpec extends ObjectBehavior
{
    function let(VariantInterface $variant)
    {
        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Product::class);
    }

    function it_implements_a_product_interface()
    {
        $this->shouldImplement(ProductInterface::class);
    }

    function it_implements_an_image_aware_interface()
    {
        $this->shouldImplement(ImageAwareInterface::class);
    }

    function it_extends_a_product_model()
    {
        $this->shouldHaveType(BaseProduct::class);
    }

    function it_initializes_a_taxon_collection_by_default()
    {
        $this->getTaxons()->shouldHaveType(Collection::class);
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
            ->shouldThrow(\InvalidArgumentException::class)
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

    function it_has_no_main_taxon_by_default()
    {
        $this->getMainTaxon()->shouldReturn(null);
    }

    function it_sets_main_taxon(TaxonInterface $taxon)
    {
        $this->setMainTaxon($taxon);
        $this->getMainTaxon()->shouldReturn($taxon);
    }

    function it_returns_a_first_variant(VariantInterface $variant)
    {
        $this->addVariant($variant);

        $this->getFirstVariant()->shouldReturn($variant);
    }

    function it_returns_a_null_as_first_variant_if_a_product_has_no_variants(VariantInterface $variant)
    {
        $variant->setProduct(null)->shouldBeCalled();
        $this->removeVariant($variant);

        $this->getFirstVariant()->shouldReturn(null);
    }

    function it_returns_a_first_variants_price_as_product_price(VariantInterface $variant)
    {
        $variant->getPrice()->willReturn(1000);
        $this->addVariant($variant);

        $this->getPrice()->shouldReturn(1000);
    }

    function it_returns_a_null_as_product_price_if_a_product_has_no_variants(VariantInterface $variant)
    {
        $variant->setProduct(null)->shouldBeCalled();
        $this->removeVariant($variant);

        $this->getPrice()->shouldReturn(null);
    }

    function it_initializes_image_collection_by_default()
    {
        $this->getImages()->shouldHaveType(Collection::class);
    }

    function it_adds_an_image(ImageInterface $image)
    {
        $this->addImage($image);
        $this->hasImages()->shouldReturn(true);
        $this->hasImage($image)->shouldReturn(true);
    }

    function it_removes_an_image(ImageInterface $image)
    {
        $this->addImage($image);
        $this->removeImage($image);
        $this->hasImage($image)->shouldReturn(false);
    }

    function it_returns_an_image_by_code(ImageInterface $image)
    {
        $image->getCode()->willReturn('thumbnail');
        $image->setOwner($this)->shouldBeCalled();

        $this->addImage($image);

        $this->getImageByCode('thumbnail')->shouldReturn($image);
    }
}
