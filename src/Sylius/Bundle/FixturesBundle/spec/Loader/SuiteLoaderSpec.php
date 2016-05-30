<?php

namespace spec\Sylius\Bundle\FixturesBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Sylius\Bundle\FixturesBundle\Loader\SuiteLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteLoaderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Loader\SuiteLoader');
    }

    function it_implements_TODO_interface()
    {
        $this->shouldImplement('Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface');
    }
}
