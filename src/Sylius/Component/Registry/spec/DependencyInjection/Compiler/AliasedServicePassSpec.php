<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Registry\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\DependencyInjection\Compiler\AliasedServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Sylius\Component\Registry\ServiceRegistry;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author Daniel Leech <daniel@dantleech.com>
 */
class AliasedServicePassSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('some.registry', 'my_tag');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AliasedServicePass::class);
    }

    function it_should_return_early_if_the_container_does_not_have_the_registry_definition(
        ContainerBuilder $container
    ) {
        $container->hasDefinition('some.registry')->willReturn(false);

        $this->process($container);
    }

    function it_should_throw_an_exception_if_the_registry_class_is_not_an_instance_of_the_service_registry(
        ContainerBuilder $container,
        Definition $definition,
        ParameterBag $parameterBag
    ) {
        $container->hasDefinition('some.registry')->willReturn(true);
        $container->findDefinition('some.registry')->willReturn($definition);
        $definition->getClass()->willReturn('stdClass');
        $container->getParameterBag()->willReturn($parameterBag);
        $parameterBag->resolveValue('stdClass')->willReturn('stdClass');

        $this->shouldThrow(new \InvalidArgumentException(
            'The service registry "some.registry" must implement the "Sylius\Component\Registry\ServiceRegistryInterface" interface.'))->during('process', [ $container ]);
    }

    function it_should_throw_an_exception_if_the_tagged_definition_does_not_have_the_alias_attribute(
        ContainerBuilder $container,
        Definition $definition,
        ParameterBag $parameterBag
    ) {
        $container->hasDefinition('some.registry')->willReturn(true);
        $container->findDefinition('some.registry')->willReturn($definition);
        $definition->getClass()->willReturn(ServiceRegistry::class);
        $container->getParameterBag()->willReturn($parameterBag);
        $parameterBag->resolveValue(ServiceRegistry::class)->willReturn(ServiceRegistry::class);

        $container->findTaggedServiceIds('my_tag')->willReturn([
            'my_service' => [ [ 'foobar' => 'invalid' ] ]
        ]);

        $this->shouldThrow(new \InvalidArgumentException(
            'Service "my_service" with tag "my_tag" needs to have the "alias" attribute.'))->during('process', [ $container ]);
    }

    function it_should_add_services_to_the_registry(
        ContainerBuilder $container,
        Definition $definition,
        ParameterBag $parameterBag
    ) {
        $container->hasDefinition('some.registry')->willReturn(true);
        $container->findDefinition('some.registry')->willReturn($definition);
        $definition->getClass()->willReturn(ServiceRegistry::class);
        $container->getParameterBag()->willReturn($parameterBag);
        $parameterBag->resolveValue(ServiceRegistry::class)->willReturn(ServiceRegistry::class);

        $container->findTaggedServiceIds('my_tag')->willReturn([
            'my_service' => [ [ 'alias' => 'my_service_name' ] ]
        ]);

        $definition->addMethodCall('register', [
            'my_service_name', new Reference('my_service')
        ])->shouldBeCalled();

        $this->process($container);
    }

    function it_should_add_services_from_a_custom_tag_to_the_registry(
        ContainerBuilder $container,
        Definition $definition,
        ParameterBag $parameterBag
    ) {
        $this->beConstructedWith('my_registry', 'my_service', 'type');

        $container->hasDefinition('my_registry')->willReturn(true);
        $container->findDefinition('my_registry')->willReturn($definition);
        $definition->getClass()->willReturn(ServiceRegistry::class);
        $container->getParameterBag()->willReturn($parameterBag);
        $parameterBag->resolveValue(ServiceRegistry::class)->willReturn(ServiceRegistry::class);

        $container->findTaggedServiceIds('my_service')->willReturn([
            'my_service' => [ [ 'type' => 'my_service_name' ] ]
        ]);

        $definition->addMethodCall('register', [
            'my_service_name', new Reference('my_service')
        ])->shouldBeCalled();

        $this->process($container);
    }
}
