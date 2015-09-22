<?php

namespace spec\Sylius\Component\Metadata\Processor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Twig_Environment;

/**
 * @mixin \Sylius\Component\Metadata\Processor\TwigMetadataProcessor
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TwigMetadataProcessorSpec extends ObjectBehavior
{
    function let(Twig_Environment $twig)
    {
        $this->beConstructedWith($twig);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Processor\TwigMetadataProcessor');
    }

    function it_implements_Sylius_Metadata_Processor_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Processor\MetadataProcessorInterface');
    }

    function it_delegates_processing_directly_to_metadata(MetadataInterface $metadata)
    {
        $metadata->forAll(Argument::type('callable'))->shouldBeCalled();

        $this->process($metadata);
    }
}
