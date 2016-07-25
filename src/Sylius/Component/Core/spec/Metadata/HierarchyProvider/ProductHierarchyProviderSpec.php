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
use Sylius\Component\Core\Model\ArchetypeInterface;
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

    function it_generates_correct_hierarchy_when_product_has_archetype(
        ProductInterface $product,
        ArchetypeInterface $archetype
    ) {
        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $archetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-42');

        $product->getArchetype()->shouldBeCalled()->willReturn($archetype);
        $archetype->getParent()->shouldBeCalled()->willReturn(false);

        $this->getHierarchyByMetadataSubject($product)->shouldReturn([
            'Product-42',
            'Archetype-42',
            'Product',
            'DefaultPage',
        ]);
    }

    function it_generates_correct_hierarchy_when_product_has_archetype_hierarcy(
        ProductInterface $product,
        ArchetypeInterface $archetype,
        ArchetypeInterface $parentArchetype,
        ArchetypeInterface $grandparentArchetype
    ) {
        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $archetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-42');
        $archetype->getParent()->shouldBeCalled()->willReturn($parentArchetype);
        $parentArchetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-21');
        $parentArchetype->getParent()->shouldBeCalled()->willReturn($grandparentArchetype);
        $grandparentArchetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-10');
        $grandparentArchetype->getParent()->shouldBeCalled()->willReturn(false);

        $product->getArchetype()->shouldBeCalled()->willReturn($archetype);

        $this->getHierarchyByMetadataSubject($product)->shouldReturn([
            'Product-42',
            'Archetype-42',
            'Archetype-21',
            'Archetype-10',
            'Product',
            'DefaultPage',
        ]);
    }

    function it_generates_correct_hierarchy_when_product_does_not_have_archetype(
        ProductInterface $product
    ) {
        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $product->getArchetype()->shouldBeCalled()->willReturn(null);

        $this->getHierarchyByMetadataSubject($product)->shouldReturn([
            'Product-42',
            'Product',
            'DefaultPage',
        ]);
    }
}
