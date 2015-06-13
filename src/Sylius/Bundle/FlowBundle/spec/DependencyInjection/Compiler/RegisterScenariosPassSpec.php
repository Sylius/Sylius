<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterScenariosPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\DependencyInjection\Compiler\RegisterScenariosPass');
    }

    function it_is_compiler_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes(ContainerBuilder $container, Definition $coordinator)
    {
        $container->getDefinition('sylius.process.coordinator')->shouldBeCalled()->willreturn($coordinator);
        $container->findTaggedServiceIds('sylius.process.scenario')->shouldBeCalled()->willreturn(array(
            'id' => array(
                array(
                    'alias' => 'alias'
                )
            )
        ));

        $coordinator->addMethodCall('registerScenario', Argument::type('array'))->shouldBeCalled();

        $this->process($container);
    }
}
