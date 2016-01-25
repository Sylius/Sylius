<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterAdjustmentFactoryPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterAdjustmentFactoryPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes_with_given_container(
        ContainerBuilder $container,
        Definition $oldAdjustmentFactoryDefinition,
        Definition $newAdjustmentFactoryDefinition
    ) {
        $container->getDefinition('sylius.factory.adjustment')->willReturn($oldAdjustmentFactoryDefinition);

        $container->setDefinition('sylius.factory.adjustment', Argument::type('Symfony\Component\DependencyInjection\Definition'))->willReturn($newAdjustmentFactoryDefinition);
        $newAdjustmentFactoryDefinition->addArgument($oldAdjustmentFactoryDefinition)->shouldBeCalled();

        $this->process($container);
    }
}
