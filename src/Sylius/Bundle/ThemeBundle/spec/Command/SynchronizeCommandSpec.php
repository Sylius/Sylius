<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Command\SynchronizeCommand;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * @mixin SynchronizeCommand
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SynchronizeCommandSpec extends ObjectBehavior
{
    function let(ThemeSynchronizerInterface $themeSynchronizer)
    {
        $this->beConstructedWith($themeSynchronizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Command\SynchronizeCommand');
    }

    function it_is_a_command()
    {
        $this->shouldHaveType(Command::class);
    }
}
