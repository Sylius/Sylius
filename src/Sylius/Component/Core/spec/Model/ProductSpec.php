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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as VariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;

/**
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
        $this->shouldImplement(ImagesAwareInterface::class);
    }

    function it_extends_a_product_model()
    {
        $this->shouldHaveType(BaseProduct::class);
    }

    function it_initializes_a_product_taxon_collection_by_default()
    {
        $this->getProductTaxons()->shouldHaveType(Collection::class);
    }

    function it_adds_a_product_taxons(ProductTaxonInterface $productTaxon)
    {
        $this->addProductTaxon($productTaxon);
        $this->hasProductTaxon($productTaxon)->shouldReturn(true);
    }

    function it_removes_a_product_taxons(ProductTaxonInterface $productTaxon)
    {
        $this->addProductTaxon($productTaxon);
        $this->removeProductTaxon($productTaxon);

        $this->hasProductTaxon($productTaxon)->shouldReturn(false);
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

    function it_has_no_main_taxon_by_default()
    {
        $this->getMainTaxon()->shouldReturn(null);
    }

    function it_sets_main_taxon(TaxonInterface $taxon)
    {
        $this->setMainTaxon($taxon);
        $this->getMainTaxon()->shouldReturn($taxon);
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

    function it_returns_images_by_type(ImageInterface $image)
    {
        $image->getType()->willReturn('thumbnail');
        $image->setOwner($this)->shouldBeCalled();

        $this->addImage($image);

        $this->getImagesByType('thumbnail')->shouldBeLike(new ArrayCollection([$image->getWrappedObject()]));
    }

    function it_filters_product_taxons_by_taxon(
        ProductTaxonInterface $firstProductTaxon,
        ProductTaxonInterface $secondProductTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon
    ) {
        $this->addProductTaxon($firstProductTaxon);
        $this->addProductTaxon($secondProductTaxon);

        $firstProductTaxon->getTaxon()->willReturn($firstTaxon);
        $secondProductTaxon->getTaxon()->willReturn($secondTaxon);

        $this->filterProductTaxonsByTaxon($firstTaxon)->shouldBeLike(new ArrayCollection([$firstProductTaxon->getWrappedObject()]));
    }

    function it_returns_null_if_no_product_taxon_has_taxon_during_filtering(
        ProductTaxonInterface $firstProductTaxon,
        ProductTaxonInterface $secondProductTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        TaxonInterface $thirdTaxon
    ) {
        $this->addProductTaxon($firstProductTaxon);
        $this->addProductTaxon($secondProductTaxon);

        $firstProductTaxon->getTaxon()->willReturn($firstTaxon);
        $secondProductTaxon->getTaxon()->willReturn($secondTaxon);

        $this->filterProductTaxonsByTaxon($thirdTaxon)->shouldBeLike(new ArrayCollection());
    }
}
