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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterCalculatorsPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler\RegisterCalculatorsPass');
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes_the_calculators_services(ContainerBuilder $container, Definition $calculator)
    {
        $container->hasDefinition('sylius.registry.tax_calculator')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('sylius.registry.tax_calculator')->shouldBeCalled()->willReturn($calculator);

        $container->findTaggedServiceIds('sylius.tax_calculator')->shouldBeCalled()->willReturn([
            'calculator_id' => [
                [
                    'calculator' => 'calculator_name',
                ],
            ],
        ]);

        $calculator->addMethodCall(
            'register',
            Argument::type('array')
        )->shouldBeCalled();

        $container->setParameter(
            'sylius.tax_calculators',
            ['calculator_name' => 'calculator_name']
        )->shouldBeCalled();

        $this->process($container);
    }
}
