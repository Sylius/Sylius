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
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;

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

    function it_check_if_given_resolver_supports_a_metadata(
        MetadataHierarchyProviderInterface $firstHierarchyProvider,
        MetadataHierarchyProviderInterface $secondHierarchyProvider,
        MetadataSubjectInterface $supportedMetadataSubject,
        MetadataSubjectInterface $unsupportedMetadataSubject
    ) {
        $firstHierarchyProvider->supports($supportedMetadataSubject)->shouldBeCalled()->willReturn(false);
        $secondHierarchyProvider->supports($supportedMetadataSubject)->shouldBeCalled()->willReturn(true);

        $firstHierarchyProvider->supports($unsupportedMetadataSubject)->shouldBeCalled()->willReturn(false);
        $secondHierarchyProvider->supports($unsupportedMetadataSubject)->shouldBeCalled()->willReturn(false);

        $this->supports($supportedMetadataSubject)->shouldReturn(true);
        $this->supports($unsupportedMetadataSubject)->shouldReturn(false);
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

    function it_throws_exception_if_trying_to_resolve_unsupported_metadata(
        MetadataHierarchyProviderInterface $firstHierarchyProvider,
        MetadataHierarchyProviderInterface $secondHierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $firstHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(false);
        $secondHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(false);

        $firstHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldNotBeCalled();
        $secondHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldNotBeCalled();

        $this->shouldThrow('\InvalidArgumentException')->duringGetHierarchyByMetadataSubject($metadataSubject);
    }
}
