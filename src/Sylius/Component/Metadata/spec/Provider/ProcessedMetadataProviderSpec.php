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
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;
use Sylius\Component\Metadata\Processor\MetadataProcessorInterface;
use Sylius\Component\Metadata\Provider\MetadataProviderInterface;

/**
 * @mixin \Sylius\Component\Metadata\Provider\ProcessedMetadataProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProcessedMetadataProviderSpec extends ObjectBehavior
{
    function let(MetadataProviderInterface $metadataProvider, MetadataProcessorInterface $metadataProcessor)
    {
        $this->beConstructedWith($metadataProvider, $metadataProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Provider\ProcessedMetadataProvider');
    }

    function it_implements_Metadata_Provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Provider\MetadataProviderInterface');
    }

    function it_process_returned_metadata_if_not_null(
        MetadataProviderInterface $metadataProvider,
        MetadataProcessorInterface $metadataProcessor,
        MetadataInterface $providedMetadata,
        MetadataInterface $processedMetadata,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataProvider->findMetadataBySubject($metadataSubject)->shouldBeCalled()->willReturn($providedMetadata);

        $metadataProcessor->process($providedMetadata, ['subject' => $metadataSubject])->shouldBeCalled()->willReturn($processedMetadata);

        $this->findMetadataBySubject($metadataSubject)->shouldReturn($processedMetadata);
    }

    function it_does_not_process_returned_metadata_if_null(
        MetadataProviderInterface $metadataProvider,
        MetadataProcessorInterface $metadataProcessor,
        MetadataSubjectInterface $metadataSubject
    ) {
        $metadataProvider->findMetadataBySubject($metadataSubject)->shouldBeCalled()->willReturn(null);

        $metadataProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this->findMetadataBySubject($metadataSubject)->shouldReturn(null);
    }
}
