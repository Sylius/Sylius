<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace spec\Sylius\Component\Seo\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Seo\Compiler\MetadataCompilerInterface;
use Sylius\Component\Seo\Model\MetadataInterface;
use Sylius\Component\Seo\Model\MetadataSubjectInterface;
use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @mixin \Sylius\Component\Seo\Provider\MetadataProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $rootMetadataRepository, MetadataCompilerInterface $metadataCompiler)
    {
        $this->beConstructedWith($rootMetadataRepository, $metadataCompiler);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Seo\Provider\MetadataProvider');
    }

    function it_implements_Metadata_Provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Seo\Provider\MetadataProviderInterface');
    }

    function it_provides_metadata_by_subject(
        RepositoryInterface $rootMetadataRepository,
        MetadataCompilerInterface $metadataCompiler,
        RootMetadataInterface $rootMetadata,
        MetadataInterface $metadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataCompiler->compile($rootMetadata)->shouldBeCalled()->willReturn($metadata);

        $rootMetadataRepository->findOneBy(['key' => 'unique_key:42'])->shouldBeCalled()->willReturn($rootMetadata);

        $metadataSubject->getMetadataIdentifier()->shouldBeCalled()->willReturn('unique_key:42');

        $this->getMetadataBySubject($metadataSubject)->shouldReturn($metadata);
    }
}
