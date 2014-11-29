<?php

namespace spec\Sylius\Component\Variation\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;

class VariantGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $variantRepository)
    {
        $this->beConstructedWith($variantRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement('Sylius\Component\Variation\Generator\VariantGenerator');
    }

    function it_cannot_generate_variants_for_an_object_without_options(VariableInterface $variable)
    {
        $variable->hasOptions()->willReturn(false);

        $this->shouldThrow('InvalidArgumentException')->duringGenerate($variable);
    }
}
