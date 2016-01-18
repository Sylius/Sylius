<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Bundle\ResourceBundle\Fixture\Foo;
use spec\Sylius\Bundle\ResourceBundle\Fixture\FooInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

require_once __DIR__ . '/../Fixture/FooInterface.php';
require_once __DIR__ . '/../Fixture/Foo.php';

/**
 * Doctrine target entities resolver spec.
 * It adds proper method calls to doctrine listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DoctrineTargetEntitiesResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\DoctrineTargetEntitiesResolver');
    }

    function it_should_get_interfaces_from_the_container(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine.event_listener')->willReturn(false);
        $resolverDefinition->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata'])->shouldBeCalled();

        $container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')->willReturn(true);
        $container->findDefinition('doctrine.orm.listeners.resolve_target_entity')->willReturn($resolverDefinition);

        $container->hasParameter('sylius.resource.interface')->willReturn(true);
        $container->getParameter('sylius.resource.interface')->willReturn(FooInterface::class);

        $container->hasParameter('sylius.resource.model')->willReturn(true);
        $container->getParameter('sylius.resource.model')->willReturn(Foo::class);

        $resolverDefinition
            ->addMethodCall(
                'addResolveTargetEntity',
                [FooInterface::class, Foo::class, []]
            )
            ->shouldBeCalled()
        ;

        $this->resolve($container, [
            'sylius.resource.interface' => 'sylius.resource.model'
        ]);
    }

    function it_should_get_interfaces(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine.event_listener')->willReturn(false);
        $resolverDefinition->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata'])->shouldBeCalled();

        $container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')->willReturn(true);
        $container->findDefinition('doctrine.orm.listeners.resolve_target_entity')->willReturn($resolverDefinition);

        $container->hasParameter(RepositoryInterface::class)->willReturn(false);

        $container->hasParameter(Foo::class)->willReturn(false);

        $resolverDefinition
            ->addMethodCall(
                'addResolveTargetEntity',
                [RepositoryInterface::class, Foo::class, []]
            )
            ->shouldBeCalled()
        ;

        $this->resolve($container, [
            RepositoryInterface::class => Foo::class
        ]);
    }
}
