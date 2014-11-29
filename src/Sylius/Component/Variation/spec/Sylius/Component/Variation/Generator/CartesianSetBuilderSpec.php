<?php

namespace spec\Sylius\Component\Variation\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CartesianSetBuilderSpec extends ObjectBehavior
{
    function it_is_a_set_builder()
    {
        $this->shouldImplement('Sylius\Component\Variation\Generator\SetBuilderInterface');
    }

    function it_returns_the_same_set_as_the_Cartesian_product_when_only_one_was_given()
    {
        $set = array('a', 'b', 'c');

        $this->build(array($set), false)->shouldReturn($set);
    }

    function it_requires_an_array_of_tuple_sets_to_build_from()
    {
        $tupleSetNotInArray = array('a', 'b', 'c');

        $this->shouldThrow('InvalidArgumentException')->duringBuild($tupleSetNotInArray, Argument::any());
    }

    function it_builds_the_Cartesian_product_set_from_two_sets()
    {
        $setA = array('a', 'b', 'c');
        $setB = array('1', '2', '3');

        $this->build(array($setA, $setB), false)->shouldReturn(array(
            array('a', '1'),
            array('a', '2'),
            array('a', '3'),

            array('b', '1'),
            array('b', '2'),
            array('b', '3'),

            array('c', '1'),
            array('c', '2'),
            array('c', '3'),
        ));
    }
}
