<?php

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Exclusion;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;


class SparseFieldsetsExclusionStrategySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['foo', 'bar']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Serializer\Exclusion\SparseFieldsetsExclusionStrategy');
    }

    function it_implements_exclusion_strategy_interface()
    {
        $this->shouldImplement(ExclusionStrategyInterface::class);
    }

    function it_should_never_skip_class(PropertyMetadata $property, Context $navigatorContext)
    {
         $this->callOnWrappedObject(
            'shouldSkipProperty',
            [$property->getWrappedObject(), $navigatorContext->getWrappedObject()]
        )->shouldReturn(false);
    }
}
