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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Metadata\Compiler\MetadataCompilerInterface;
use Sylius\Component\Metadata\HierarchyProvider\MetadataHierarchyProviderInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Model\RootMetadataInterface;

/**
 * @mixin \Sylius\Component\Metadata\Provider\MetadataProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProviderSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider
    ) {
        $this->beConstructedWith($rootMetadataRepository, $metadataCompiler, $metadataHierarchyProvider);
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
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        RootMetadataInterface $rootMetadata,
        MetadataInterface $metadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(true);
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn($rootMetadata);
        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn(null);

        $rootMetadata->getMetadata()->shouldBeCalled()->willReturn($metadata);

        $metadataCompiler->compile($metadata, [])->shouldBeCalled()->willReturn($metadata);

        $this->getMetadataBySubject($metadataSubject)->shouldReturn($metadata);
    }

    function it_provides_metadata_with_parent_only_by_subject(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        RootMetadataInterface $rootMetadata,
        MetadataInterface $metadata,
        MetadataInterface $compiledMetadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(true);
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn(null);
        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn($rootMetadata);

        $rootMetadata->getMetadata()->shouldBeCalled()->willReturn($metadata);

        $metadataCompiler->compile($metadata, [])->shouldBeCalled()->willReturn($compiledMetadata);

        $this->getMetadataBySubject($metadataSubject)->shouldReturn($compiledMetadata);
    }

    function it_provides_metadata_with_both_child_and_parents_by_subject(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        RootMetadataInterface $rootChildMetadata,
        RootMetadataInterface $rootParentMetadata,
        MetadataInterface $childMetadata,
        MetadataInterface $parentMetadata,
        MetadataInterface $compiledMetadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(true);
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn($rootChildMetadata);
        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn($rootParentMetadata);

        $rootChildMetadata->getMetadata()->shouldBeCalled()->willReturn($childMetadata);
        $rootParentMetadata->getMetadata()->shouldBeCalled()->willReturn($parentMetadata);

        $metadataCompiler->compile($childMetadata, [$parentMetadata])->shouldBeCalled()->willReturn($compiledMetadata);

        $this->getMetadataBySubject($metadataSubject)->shouldReturn($compiledMetadata);
    }

    function it_returns_null_if_metadata_is_not_found(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(true);
        $metadataHierarchyProvider->getHierarchyByMetadataSubject($metadataSubject)->shouldBeCalled()->willReturn([
            'MetadataSubject-42',
            'MetadataSubject',
        ]);

        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled()->willReturn(null);
        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled()->willReturn(null);

        $metadataCompiler->compile(Argument::cetera())->shouldNotBeCalled();

        $this->getMetadataBySubject($metadataSubject)->shouldReturn(null);
    }

    function it_creates_default_hierarchy_if_there_is_no_suitable_hierarchy_provider(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        MetadataHierarchyProviderInterface $metadataHierarchyProvider,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataHierarchyProvider->supports($metadataSubject)->shouldBeCalled()->willReturn(false);

        $metadataSubject->getMetadataIdentifier()->shouldBeCalled()->willReturn('MetadataSubject-42');
        $metadataSubject->getMetadataClassIdentifier()->shouldBeCalled()->willReturn('MetadataSubject');

        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject-42'])->shouldBeCalled();
        $rootMetadataRepository->findOneBy(['id' => 'MetadataSubject'])->shouldBeCalled();

        $metadataCompiler->compile(Argument::cetera());

        $this->getMetadataBySubject($metadataSubject);
    }
}
