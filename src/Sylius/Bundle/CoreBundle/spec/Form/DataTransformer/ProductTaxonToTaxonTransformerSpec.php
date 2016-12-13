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
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductTaxonToTaxonTransformerSpec extends ObjectBehavior
{
    function let(FactoryInterface $productTaxonFactory)
    {
        $this->beConstructedWith($productTaxonFactory);
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

    function it_transforms_taxon_to_product_taxon(
        ProductTaxonInterface $productTaxon,
        TaxonInterface $taxon,
        FactoryInterface $productTaxonFactory
    ) {
        $productTaxonFactory->createNew()->willReturn($productTaxon);
        $productTaxon->setTaxon($taxon)->shouldBeCalled();

        $this->reverseTransform($taxon)->shouldReturn($productTaxon);
    }

    function it_returns_null_during_reverse_transform()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_throws_invalid_argument_exception_during_transforms()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('transform', [new \stdClass()]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('reverseTransform', [new \stdClass()]);
    }
}
