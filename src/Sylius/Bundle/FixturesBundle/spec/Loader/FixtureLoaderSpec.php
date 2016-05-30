<?php

namespace spec\Sylius\Bundle\FixturesBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Sylius\Bundle\FixturesBundle\Loader\FixtureLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureLoaderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Loader\FixtureLoader');
    }

    function it_implements_TODO_interface()
    {
        $this->shouldImplement('Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface');
    }
}
