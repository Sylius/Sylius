<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Metadata\HierarchyProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Core\Model\ArchetypeInterface;
use Sylius\Core\Model\ProductInterface;
use Sylius\Core\Model\ProductVariantInterface;
use Sylius\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Metadata\Model\MetadataSubjectInterface;

/**
 * @mixin \Sylius\Core\Metadata\HierarchyProvider\ProductVariantHierarchyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductVariantHierarchyProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Metadata\HierarchyProvider\ProductVariantHierarchyProvider');
    }

    function it_implements_Sylius_Metadata_Hierarchy_Provider_interface()
    {
        $this->shouldImplement(MetadataHierarchyProviderInterface::class);
    }

    function it_supports_Sylius_Core_ProductVariant(
        ProductVariantInterface $coreProductVariant,
        MetadataSubjectInterface $metadataSubject
    ) {
        $this->supports($coreProductVariant)->shouldReturn(true);
        $this->supports($metadataSubject)->shouldReturn(false);
    }

    function it_generates_correct_hierarchy_when_product_variant_has_archetype(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArchetypeInterface $archetype
    ) {
        $productVariant->getMetadataIdentifier()->shouldBeCalled()->willReturn('ProductVariant-42');

        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $archetype->getParent()->shouldBeCalled()->willReturn(null);
        $archetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-42');

        $productVariant->getProduct()->shouldBeCalled()->willReturn($product);

        $product->getArchetype()->shouldBeCalled()->willReturn($archetype);

        $this->getHierarchyByMetadataSubject($productVariant)->shouldReturn([
            'ProductVariant-42',
            'Product-42',
            'Archetype-42',
            'Product',
            'DefaultPage',
        ]);
    }

    function it_generates_correct_hierarchy_when_product_variant_has_archetype_hierarcy(
        ProductVariantInterface $productVariant,
        ProductInterface $product,
        ArchetypeInterface $archetype,
        ArchetypeInterface $parentArchetype,
        ArchetypeInterface $grandparentArchetype
    ) {
        $productVariant->getMetadataIdentifier()->shouldBeCalled()->willReturn('ProductVariant-42');

        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $archetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-42');
        $archetype->getParent()->shouldBeCalled()->willReturn($parentArchetype);
        $parentArchetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-21');
        $parentArchetype->getParent()->shouldBeCalled()->willReturn($grandparentArchetype);
        $grandparentArchetype->getMetadataIdentifier()->shouldBeCalled()->willReturn('Archetype-10');
        $grandparentArchetype->getParent()->shouldBeCalled()->willReturn(null);

        $productVariant->getProduct()->shouldBeCalled()->willReturn($product);

        $product->getArchetype()->shouldBeCalled()->willReturn($archetype);

        $this->getHierarchyByMetadataSubject($productVariant)->shouldReturn([
            'ProductVariant-42',
            'Product-42',
            'Archetype-42',
            'Archetype-21',
            'Archetype-10',
            'Product',
            'DefaultPage',
        ]);
    }

    function it_generates_correct_hierarchy_when_product_variant_does_not_have_archetype(
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ) {
        $productVariant->getMetadataIdentifier()->shouldBeCalled()->willReturn('ProductVariant-42');

        $product->getMetadataIdentifier()->shouldBeCalled()->willReturn('Product-42');
        $product->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Product');

        $productVariant->getProduct()->shouldBeCalled()->willReturn($product);

        $product->getArchetype()->shouldBeCalled()->willReturn(null);

        $this->getHierarchyByMetadataSubject($productVariant)->shouldReturn([
            'ProductVariant-42',
            'Product-42',
            'Product',
            'DefaultPage',
        ]);
    }
}
