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

namespace spec\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\FileInterface;
use Sylius\Component\Core\Model\FilesAwareInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductVariantInterface as VariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\Product as BaseProduct;

final class ProductSpec extends ObjectBehavior
{
    function let(VariantInterface $variant): void
    {
        $variant->setProduct($this)->shouldBeCalled();

        $this->addVariant($variant);
    }

    function it_implements_a_product_interface(): void
    {
        $this->shouldImplement(ProductInterface::class);
    }

    function it_implements_an_file_aware_interface(): void
    {
        $this->shouldImplement(FilesAwareInterface::class);
    }

    function it_extends_a_product_model(): void
    {
        $this->shouldHaveType(BaseProduct::class);
    }

    function it_initializes_a_product_taxon_collection_by_default(): void
    {
        $this->getProductTaxons()->shouldHaveType(Collection::class);
    }

    function it_adds_a_product_taxons(ProductTaxonInterface $productTaxon): void
    {
        $this->addProductTaxon($productTaxon);
        $this->hasProductTaxon($productTaxon)->shouldReturn(true);
    }

    function it_removes_a_product_taxons(ProductTaxonInterface $productTaxon): void
    {
        $this->addProductTaxon($productTaxon);
        $this->removeProductTaxon($productTaxon);

        $this->hasProductTaxon($productTaxon)->shouldReturn(false);
    }

    function its_variant_selection_method_is_choice_by_default(): void
    {
        $this->getVariantSelectionMethod()->shouldReturn(Product::VARIANT_SELECTION_CHOICE);
    }

    function its_variant_selection_method_can_be_changed_to_option_match(): void
    {
        $this->setVariantSelectionMethod(Product::VARIANT_SELECTION_MATCH);
    }

    function it_throws_exception_if_any_other_value_is_given_as_variant_selection_method(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringSetVariantSelectionMethod('foo')
        ;
    }

    function it_has_no_main_taxon_by_default(): void
    {
        $this->getMainTaxon()->shouldReturn(null);
    }

    function it_sets_main_taxon(TaxonInterface $taxon): void
    {
        $this->setMainTaxon($taxon);
        $this->getMainTaxon()->shouldReturn($taxon);
    }

    function it_initializes_fle_collection_by_default(): void
    {
        $this->getFiles()->shouldHaveType(Collection::class);
    }

    function it_adds_a_file(FileInterface $file): void
    {
        $this->addFile($file);
        $this->hasFiles()->shouldReturn(true);
        $this->hasFile($file)->shouldReturn(true);
    }

    function it_removes_a_file(FileInterface $file): void
    {
        $this->addFile($file);
        $this->removeFile($file);
        $this->hasFile($file)->shouldReturn(false);
    }

    function it_returns_files_by_type(FileInterface $file): void
    {
        $file->getType()->willReturn('thumbnail');
        $file->setOwner($this)->shouldBeCalled();

        $this->addFile($file);

        $this->getFilesByType('thumbnail')->shouldBeLike(new ArrayCollection([$file->getWrappedObject()]));
    }

    function it_proxies_taxon_collection(ProductTaxonInterface $productTaxon, TaxonInterface $taxon, TaxonInterface $otherTaxon): void
    {
        $productTaxon->getTaxon()->willReturn($taxon);
        $productTaxon->setProduct($this)->shouldBeCalled();

        $this->addProductTaxon($productTaxon);

        $this->getTaxons()->toArray()->shouldReturn([$taxon]);
        $this->hasTaxon($taxon)->shouldReturn(true);
        $this->hasTaxon($otherTaxon)->shouldReturn(false);
    }
}
