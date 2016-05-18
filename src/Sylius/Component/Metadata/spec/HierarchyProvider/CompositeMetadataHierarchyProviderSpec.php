<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\HierarchyProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @mixin \Sylius\Component\Metadata\HierarchyProvider\CompositeMetadataHierarchyProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeMetadataHierarchyProviderSpec extends ObjectBehavior
{
    function let(MetadataHierarchyProviderInterface $firstHierarchyProvider, MetadataHierarchyProviderInterface $secondHierarchyProvider)
    {
        $this->beConstructedWith([$firstHierarchyProvider, $secondHierarchyProvider]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\HierarchyProvider\CompositeMetadataHierarchyProvider');
    }

    function it_implements_Metadata_Parent_Resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface');
    }

    function it_supports_every_metadata_subject(MetadataSubjectInterface $metadataSubject)
    {
        $this->supports($metadataSubject)->shouldReturn(true);
    }

    function it_delegates_resolving_to_correct_resolver(
        MetadataHierarchyProviderInterface $firstHierarchyProvider,
        MetadataHierarchyProviderInterface $secondHierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $firstHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(false);
        $secondHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(true);

        $firstHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldNotBeCalled();
        $secondHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn(['Child-42', 'Child', 'Parent-42']);

        $this->getHierarchyByMetadataSubject($metadataSubject)->shouldReturn(['Child-42', 'Child', 'Parent-42']);
    }

    function it_returns_default_hierarchy_if_can_not_find_supporting_hierarchy_provider(
        MetadataHierarchyProviderInterface $hierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $this->beConstructedWith([$hierarchyProvider]);

        $hierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(false);
        $hierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldNotBeCalled();

        $metadataSubject->getMetadataIdentifier()->shouldBeCalled()->willReturn('Metadata-42');
        $metadataSubject->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('Metadata');

        $this->getHierarchyByMetadataSubject($metadataSubject)->shouldReturn(['Metadata-42', 'Metadata']);
    }
}
