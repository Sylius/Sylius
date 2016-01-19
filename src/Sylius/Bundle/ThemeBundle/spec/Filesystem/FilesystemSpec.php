<?php

namespace spec\Sylius\Bundle\ThemeBundle\Filesystem;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Sylius\Bundle\ThemeBundle\Filesystem\Filesystem
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FilesystemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Filesystem\Filesystem');
    }

    function it_implements_TODO_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Filesystem\FilesystemInterface');
    }
}
