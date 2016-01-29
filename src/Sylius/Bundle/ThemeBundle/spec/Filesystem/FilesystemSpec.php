<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Filesystem;

use PhpSpec\ObjectBehavior;

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
