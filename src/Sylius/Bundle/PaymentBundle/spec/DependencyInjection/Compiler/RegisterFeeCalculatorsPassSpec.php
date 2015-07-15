<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RegisterFeeCalculatorsPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\DependencyInjection\Compiler\RegisterFeeCalculatorsPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_processes_with_given_container(ContainerBuilder $container, Definition $feeCalculatorDefinition)
    {
        $container->hasDefinition('sylius.registry.payment.fee_calculator')->willReturn(true)->shouldBeCalled();
        $container->getDefinition('sylius.registry.payment.fee_calculator')->willReturn($feeCalculatorDefinition)->shouldBeCalled();

        $feeCalculatorServices = array(
            'sylius.form.type.fee_calculator.test' => array(
                array('calculator' => 'test', 'label' => 'Test fee calculator'),
            ),
        );
        $container->findTaggedServiceIds('sylius.payment.fee_calculator')->willReturn($feeCalculatorServices);

        $feeCalculatorDefinition->addMethodCall('register', array('test', new Reference('sylius.form.type.fee_calculator.test')))->shouldBeCalled();
        $container->setParameter('sylius.payment.fee_calculators', array('test' => 'Test fee calculator'))->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_container_has_no_proper_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.payment.fee_calculator')->willReturn(false)->shouldBeCalled();
        $container->getDefinition('sylius.registry.payment.fee_calculator')->shouldNotBeCalled();

        $this->process($container);
    }

    function it_throws_exception_if_any_fee_calculator_has_improper_attributes(ContainerBuilder $container, Definition $feeCalculatorDefinition)
    {
        $container->hasDefinition('sylius.registry.payment.fee_calculator')->willReturn(true)->shouldBeCalled();
        $container->getDefinition('sylius.registry.payment.fee_calculator')->willReturn($feeCalculatorDefinition)->shouldBeCalled();

        $feeCalculatorServices = array(
            'sylius.form.type.fee_calculator.test' => array(
                array('calculator' => 'test'),
            ),
        );
        $container->findTaggedServiceIds('sylius.payment.fee_calculator')->willReturn($feeCalculatorServices);

        $this->shouldThrow(new \InvalidArgumentException('Tagged fee calculators needs to have `fee_calculator` and `label` attributes.'))->during('process', array($container));
    }
}
