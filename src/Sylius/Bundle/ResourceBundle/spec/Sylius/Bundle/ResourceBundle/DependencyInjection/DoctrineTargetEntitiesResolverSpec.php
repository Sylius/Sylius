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

use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Doctrine target entities resolver spec.
 * It adds proper method calls to doctrine listener.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DoctrineTargetEntitiesResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\DoctrineTargetEntitiesResolver');
    }

    function it_should_get_interfaces_from_the_container(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine.event_listener')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'))
            ->shouldBeCalled();

        $container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition('doctrine.orm.listeners.resolve_target_entity')
            ->shouldBeCalled()
            ->willReturn($resolverDefinition);

        $container->hasParameter('sylius.resource.interface')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->getParameter('sylius.resource.interface')
            ->shouldBeCalled()
            ->willReturn('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\FooInterface');

        $container->hasParameter('sylius.resource.model')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->getParameter('sylius.resource.model')
            ->shouldBeCalled()
            ->willReturn('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo');

        $resolverDefinition->addMethodCall(
            'addResolveTargetEntity',
            array(
                'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\FooInterface', 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo', array()
            ))->shouldBeCalled();

        $this->resolve($container, array(
            'sylius.resource.interface' => 'sylius.resource.model'
        ));
    }

    function it_should_get_interfaces(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine.event_listener')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'))
            ->shouldBeCalled();

        $container->hasDefinition('doctrine.orm.listeners.resolve_target_entity')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition('doctrine.orm.listeners.resolve_target_entity')
            ->shouldBeCalled()
            ->willReturn($resolverDefinition);

        $container->hasParameter('Sylius\Component\Resource\Repository\RepositoryInterface')
            ->shouldBeCalled()
            ->willReturn(false);

        $container->hasParameter('spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addMethodCall(
            'addResolveTargetEntity',
            array(
                'Sylius\Component\Resource\Repository\RepositoryInterface', 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo', array()
            ))->shouldBeCalled();

        $this->resolve($container, array(
            'Sylius\Component\Resource\Repository\RepositoryInterface' => 'spec\Sylius\Bundle\ResourceBundle\Fixture\Entity\Foo'
        ));
    }
}
