<?php

namespace spec\Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareLoaderDecoratorPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @mixin ThemeAwareLoaderDecoratorPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeAwareLoaderDecoratorPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareLoaderDecoratorPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }
}


