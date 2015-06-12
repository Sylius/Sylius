<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace spec\Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterCalculatorsPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler\RegisterCalculatorsPass');
    }

    function it_is_a_coplier_pass()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes_the_calculators_services(ContainerBuilder $container, Definition $calculator)
    {
        $container->hasDefinition('sylius.tax_calculator')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('sylius.tax_calculator')->shouldBeCalled()->willReturn($calculator);

        $container->findTaggedServiceIds('sylius.tax_calculator')->shouldBeCalled()->willReturn(array(
            'calculator_id' => array(
                array(
                    'calculator' => 'calculator_name'
                )
            )
        ));

        $calculator->addMethodCall(
            'registerCalculator',
            Argument::type('array')
        )->shouldBeCalled();

        $container->setParameter(
            'sylius.tax_calculators',
            array('calculator_name' => 'calculator_name')
        )->shouldBeCalled();

        $this->process($container);
    }
}
