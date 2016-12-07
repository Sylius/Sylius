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
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductTaxonsToTaxonsTransformer;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonsToTaxonsTransformerSpec extends ObjectBehavior
{
    function let(FactoryInterface $productTaxonFactory)
    {
        $this->beConstructedWith($productTaxonFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductTaxonsToTaxonsTransformer::class);
    }
    
    function it_implements_data_transformer_interface()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_product_taxons_collection_to_taxon_collection(
        ProductTaxonInterface $firstProductTaxon,
        ProductTaxonInterface $secondProductTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon
    ) {
        $productTaxons = [$firstProductTaxon->getWrappedObject(), $secondProductTaxon->getWrappedObject()];

        $firstProductTaxon->getTaxon()->willReturn($firstTaxon);
        $secondProductTaxon->getTaxon()->willReturn($secondTaxon);

        $taxons = [$firstTaxon->getWrappedObject(), $secondTaxon->getWrappedObject()];

        $this->transform($productTaxons)->shouldReturn($taxons);
    }

    function it_returns_empty_collection_during_transform()
    {
        $this->transform(null)->shouldReturn([]);
    }

    function it_transforms_taxons_collection_to_product_taxons_collection(
        ProductTaxonInterface $productTaxon,
        TaxonInterface $taxon,
        FactoryInterface $productTaxonFactory
    ) {
        $productTaxons = [$productTaxon->getWrappedObject()];
        $taxons = [$taxon->getWrappedObject()];

        $productTaxonFactory->createNew()->willReturn($productTaxon);
        $productTaxon->setTaxon($taxon)->shouldBeCalled();

        $this->reverseTransform($taxons)->shouldReturn($productTaxons);
    }

    function it_returns_empty_collection_during_reverse_transform()
    {
        $this->reverseTransform(null)->shouldReturn([]);
    }
}
