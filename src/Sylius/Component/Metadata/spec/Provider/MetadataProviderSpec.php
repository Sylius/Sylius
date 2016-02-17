<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Compiler\MetadataCompilerInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\MetadataContainerInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin \Sylius\Component\Metadata\Provider\MetadataProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider
    ) {
        $this->beConstructedWith($metadataContainerRepository, $metadataCompiler, $metadataHierarchyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Provider\MetadataProvider');
    }

    function it_implements_Metadata_Provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Provider\MetadataProviderInterface');
    }

    function it_provides_metadata_with_child_only_by_subject(
        RepositoryInterface $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataContainerInterface $metadataContainer,
        MetadataInterface $metadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn($metadataContainer);
        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn(null);

        $metadataContainer->getMetadata()->shouldBeCalled()->willReturn($metadata);

        $metadataCompiler->compile($metadata, [])->shouldBeCalled()->willReturn($metadata);

        $this->findMetadataBySubject($metadataSubject)->shouldReturn($metadata);
    }

    function it_provides_metadata_with_parent_only_by_subject(
        RepositoryInterface $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataContainerInterface $metadataContainer,
        MetadataInterface $metadata,
        MetadataInterface $compiledMetadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn(null);
        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn($metadataContainer);

        $metadataContainer->getMetadata()->shouldBeCalled()->willReturn($metadata);

        $metadataCompiler->compile($metadata, [])->shouldBeCalled()->willReturn($compiledMetadata);

        $this->findMetadataBySubject($metadataSubject)->shouldReturn($compiledMetadata);
    }

    function it_provides_metadata_with_both_child_and_parents_by_subject(
        RepositoryInterface $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataContainerInterface $rootChildMetadata,
        MetadataContainerInterface $rootParentMetadata,
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata,
        MetadataInterface $compiledMetadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn($rootChildMetadata);
        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn($rootParentMetadata);

        $rootChildMetadata->getMetadata()->shouldBeCalled()->willReturn($childMetadata);
        $rootParentMetadata->getMetadata()->shouldBeCalled()->willReturn($parentMetadata);

        $metadataCompiler->compile($childMetadata, [$parentMetadata])->shouldBeCalled()->willReturn($compiledMetadata);

        $this->findMetadataBySubject($metadataSubject)->shouldReturn($compiledMetadata);
    }

    function it_returns_null_if_metadata_is_not_found(
        RepositoryInterface $metadataContainerRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn(null);
        $metadataContainerRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn(null);

        $metadataCompiler->compile(Argument::cetera())->shouldNotBeCalled();

        $this->findMetadataBySubject($metadataSubject)->shouldReturn(null);
    }
}
