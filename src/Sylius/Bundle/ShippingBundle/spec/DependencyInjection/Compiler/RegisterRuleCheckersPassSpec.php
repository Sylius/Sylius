<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterRuleCheckersPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass');
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes_the_calculators_services(ContainerBuilder $container, Definition $ruleChecker)
    {
        $container->hasDefinition('sylius.registry.shipping_rule_checker')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('sylius.registry.shipping_rule_checker')->shouldBeCalled()->willReturn($ruleChecker);

        $container->findTaggedServiceIds('sylius.shipping_rule_checker')->shouldBeCalled()->willReturn([
            'rule_checker_id' => [
                [
                    'type' => 'rule_checker_name',
                    'label' => 'rule_checker_label',
                ],
            ],
        ]);

        $ruleChecker->addMethodCall(
            'register',
            Argument::type('array')
        )->shouldBeCalled();

        $container->setParameter(
            'sylius.shipping_rules',
            ['rule_checker_name' => 'rule_checker_label']
        )->shouldBeCalled();

        $this->process($container);
    }
}
