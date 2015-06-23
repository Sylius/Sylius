<?php

namespace spec\Sylius\Bundle\GridBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterColumnTypesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterColumnTypesPass');
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes_the_calculators_services(ContainerBuilder $container, Definition $calculator)
    {
        $container->hasDefinition('sylius.registry.grid_column_type')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('sylius.registry.grid_column_type')->shouldBeCalled()->willReturn($calculator);

        $container->findTaggedServiceIds('sylius.grid_column_type')->shouldBeCalled()->willReturn(array(
            'calculator_id' => array(
                array(
                    'type' => 'colum_type',
                )
            )
        ));

        $calculator->addMethodCall(
            'register',
            Argument::type('array')
        )->shouldBeCalled();

        $this->process($container);
    }
}
