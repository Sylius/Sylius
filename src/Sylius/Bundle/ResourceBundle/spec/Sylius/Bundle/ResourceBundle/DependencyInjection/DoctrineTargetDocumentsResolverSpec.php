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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Doctrine target documents resolver spec.
 * It adds proper method calls to doctrine listener.
 *
 * @author Ivannis Suárez Jérez <ivannis.suarez@gmail.com>
 */
class DoctrineTargetDocumentsResolverSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\DependencyInjection\DoctrineTargetDocumentsResolver');
    }

    function it_should_get_interfaces_from_the_container(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine_mongodb.odm.event_listener')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addTag('doctrine_mongodb.odm.event_listener', array('event' => 'loadClassMetadata'))
            ->shouldBeCalled();

        $container->hasDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn($resolverDefinition);

        $container->hasParameter('sylius.resource.interface')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->getParameter('sylius.resource.interface')
            ->shouldBeCalled()
            ->willReturn('spec\Sylius\Bundle\ResourceBundle\Fixture\Document\FooInterface');

        $container->hasParameter('sylius.resource.model')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->getParameter('sylius.resource.model')
            ->shouldBeCalled()
            ->willReturn('spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo');

        $resolverDefinition->addMethodCall(
            'addResolveTargetDocument',
            array(
                'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\FooInterface', 'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo', array()
            ))->shouldBeCalled();

        $this->resolve($container, array(
            'sylius.resource.interface' => 'sylius.resource.model'
        ));
    }

    function it_should_get_interfaces(ContainerBuilder $container, Definition $resolverDefinition)
    {
        $resolverDefinition->hasTag('doctrine_mongodb.odm.event_listener')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addTag('doctrine_mongodb.odm.event_listener', array('event' => 'loadClassMetadata'))
            ->shouldBeCalled();

        $container->hasDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition('doctrine_mongodb.odm.listeners.resolve_target_document')
            ->shouldBeCalled()
            ->willReturn($resolverDefinition);

        $container->hasParameter('Sylius\Component\Resource\Repository\RepositoryInterface')
            ->shouldBeCalled()
            ->willReturn(false);

        $container->hasParameter('spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo')
            ->shouldBeCalled()
            ->willReturn(false);

        $resolverDefinition->addMethodCall(
            'addResolveTargetDocument',
            array(
                'Sylius\Component\Resource\Repository\RepositoryInterface', 'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo', array()
            ))->shouldBeCalled();

        $this->resolve($container, array(
            'Sylius\Component\Resource\Repository\RepositoryInterface' => 'spec\Sylius\Bundle\ResourceBundle\Fixture\Document\Foo'
        ));
    }
}
