<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductTaxonToTaxonTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonToTaxonTransformerSpec extends ObjectBehavior
{
    function let(FactoryInterface $productTaxonFactory, RepositoryInterface $productTaxonRepository, ProductInterface $product)
    {
        $this->beConstructedWith($productTaxonFactory, $productTaxonRepository, $product);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductTaxonToTaxonTransformer::class);
    }

    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_product_taxon_to_taxon(
        ProductTaxonInterface $productTaxon,
        TaxonInterface $taxon
    ) {
        $productTaxon->getTaxon()->willReturn($taxon);

        $this->transform($productTaxon)->shouldReturn($taxon);
    }

    function it_returns_null_during_transform()
    {
        $this->transform(null)->shouldReturn(null);
    }

    function it_transforms_taxon_to_new_product_taxon(
        FactoryInterface $productTaxonFactory,
        RepositoryInterface $productTaxonRepository,
        ProductInterface $product,
        ProductTaxonInterface $productTaxon,
        TaxonInterface $taxon
    ) {
        $productTaxonRepository->findOneBy(['taxon' => $taxon, 'product' => $product])->willReturn(null);
        $productTaxonFactory->createNew()->willReturn($productTaxon);
        $productTaxon->setTaxon($taxon)->shouldBeCalled();
        $productTaxon->setProduct($product)->shouldBeCalled();

        $this->reverseTransform($taxon)->shouldReturn($productTaxon);
    }

    function it_transforms_taxon_to_existing_product_taxon(
        RepositoryInterface $productTaxonRepository,
        ProductTaxonInterface $productTaxon,
        ProductInterface $product,
        TaxonInterface $taxon
    ) {
        $productTaxonRepository->findOneBy(['taxon' => $taxon, 'product' => $product])->willReturn($productTaxon);

        $this->reverseTransform($taxon)->shouldReturn($productTaxon);
    }

    function it_returns_null_during_reverse_transform()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_throws_transformation_failed_exception_during_transforms()
    {
        $this->shouldThrow(TransformationFailedException::class)->during('transform', [new \stdClass()]);
        $this->shouldThrow(TransformationFailedException::class)->during('reverseTransform', [new \stdClass()]);
    }
}
