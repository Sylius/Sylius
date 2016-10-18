<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler\RegisterAttributeFactoryPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RegisterAttributeFactoryPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RegisterAttributeFactoryPass::class);
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_processes_with_given_container(
        ContainerBuilder $container,
        Definition $attributeTypeRegistryDefinition,
        Definition $oldAttributeFactoryDefinition,
        Definition $newAttributeFactoryDefinition
    ) {
        $container->hasDefinition('sylius.registry.attribute_type')->willReturn(true);
        $container->getDefinition('sylius.registry.attribute_type')->willReturn($attributeTypeRegistryDefinition);

        $container->getParameter('sylius.attribute.subjects')->willReturn(['product' => []]);

        $container->getDefinition('sylius.factory.product_attribute')->willReturn($oldAttributeFactoryDefinition);

        $container
            ->setDefinition(
                'sylius.factory.product_attribute',
                Argument::type('Symfony\Component\DependencyInjection\Definition')
            )
            ->willReturn($newAttributeFactoryDefinition)
        ;
        $newAttributeFactoryDefinition->addArgument($oldAttributeFactoryDefinition)->shouldBeCalled();
        $newAttributeFactoryDefinition->addArgument($attributeTypeRegistryDefinition)->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_container_has_not_proper_definition(ContainerBuilder $container)
    {
        $container->hasDefinition('sylius.registry.attribute_type')->willReturn(false);
        $container->getDefinition('sylius.registry.attribute_type')->shouldNotBeCalled();

        $this->process($container);
    }
}
