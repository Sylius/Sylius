<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin \Sylius\Bundle\ResourceBundle\Controller\ResourceResolver
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceResolverSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, FactoryInterface $factory)
    {
        $this->beConstructedWith($repository, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceResolver');
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_searching_for_resources(
        RepositoryInterface $repository,
        RequestConfiguration $configuration
    ) {
        $configuration->getRepositoryMethod('findBy')->willReturn('findAll');
        $configuration->getRepositoryArguments(array())->willReturn(array(5));

        $repository->findAll(5)->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->getResource($configuration, 'findBy')->shouldReturn(array('foo', 'bar'));
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_creating_resource(
        FactoryInterface $factory,
        RequestConfiguration $configuration
    ) {
        $configuration->getFactoryMethod('createNew')->willReturn('createNew');
        $configuration->getFactoryArguments(array())->willReturn(array('00032'));

        $factory->createNew('00032')->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->createResource($configuration, 'createNew')->shouldReturn(array('foo', 'bar'));
    }
}
