<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\SetBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class CartesianSetBuilderSpec extends ObjectBehavior
{
    function it_is_a_set_builder()
    {
        $this->shouldImplement('Sylius\Component\Variation\SetBuilder\SetBuilderInterface');
    }

    function it_requires_an_array_of_set_tuples_to_build_from()
    {
        $tupleSetNotInArray = array('a', 'b', 'c');

        $this->shouldThrow('InvalidArgumentException')->duringBuild($tupleSetNotInArray, Argument::any());
    }

    function it_requires_at_least_one_set_tuple()
    {
        $this->shouldThrow('InvalidArgumentException')->duringBuild(array(), Argument::any());
    }

    function it_returns_the_same_set_as_the_Cartesian_product_when_only_one_was_given()
    {
        $set = array('a', 'b', 'c');

        $this->build(array($set), false)->shouldReturn($set);
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

    function it_builds_the_Cartesian_product_set_from_more_than_two_sets()
    {
        $setA = array('a', 'b', 'c');
        $setB = array('1', '2', '3');
        $setC = array('!', '@', '$');

        $this->build(array($setA, $setB, $setC), false)->shouldReturn(array(
            array('a', '1', '!'), array('a', '1', '@'), array('a', '1', '$'),
            array('a', '2', '!'), array('a', '2', '@'), array('a', '2', '$'),
            array('a', '3', '!'), array('a', '3', '@'), array('a', '3', '$'),

            array('b', '1', '!'), array('b', '1', '@'), array('b', '1', '$'),
            array('b', '2', '!'), array('b', '2', '@'), array('b', '2', '$'),
            array('b', '3', '!'), array('b', '3', '@'), array('b', '3', '$'),

            array('c', '1', '!'), array('c', '1', '@'), array('c', '1', '$'),
            array('c', '2', '!'), array('c', '2', '@'), array('c', '2', '$'),
            array('c', '3', '!'), array('c', '3', '@'), array('c', '3', '$'),
        ));
    }
}
