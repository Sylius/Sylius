<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Metadata\HierarchyProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Metadata\HierarchyProvider\ProductHierarchyProvider;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @mixin ProductHierarchyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProductHierarchyProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Metadata\HierarchyProvider\ProductHierarchyProvider');
    }

    function it_implements_Sylius_Metadata_Hierarchy_Provider_interface()
    {
        $this->shouldImplement(MetadataHierarchyProviderInterface::class);
    }

    function it_supports_Sylius_Core_Product(
        ProductInterface $coreProduct,
        MetadataSubjectInterface $metadataSubject
    ) {
        $this->supports($coreProduct)->shouldReturn(true);
        $this->supports($metadataSubject)->shouldReturn(false);
    }

    function it_generates_correct_hierarchy(ProductInterface $product)
    {
        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $this->getHierarchyByMetadataSubject($product)->shouldReturn([
            'Product-42',
            'Product',
            'DefaultPage',
        ]);
    }
}
