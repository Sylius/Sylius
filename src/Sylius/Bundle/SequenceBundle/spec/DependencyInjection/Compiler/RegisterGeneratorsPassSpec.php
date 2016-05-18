<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SequenceBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RegisterGeneratorsPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SequenceBundle\DependencyInjection\Compiler\RegisterGeneratorsPass');
    }

    public function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_processes_the_calculators_services(ContainerBuilder $container, Definition $registry)
    {
        $container->hasDefinition('sylius.registry.number_generator')->shouldBeCalled()->willReturn(true);
        $container->getDefinition('sylius.registry.number_generator')->shouldBeCalled()->willReturn($registry);

        $container->getParameter('sylius.sequence.generators')->shouldBeCalled()->willReturn(['generator']);

        $registry->addMethodCall('register', Argument::type('array'))->shouldBeCalled();

        $this->process($container);
    }
}
