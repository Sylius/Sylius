<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Resource resolver spec.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceResolverSpec extends ObjectBehavior
{
    function let(ResourceRepositoryInterface $repository, ResourceFactoryInterface $factory)
    {
        $this->beConstructedWith($repository, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceResolver');
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_searching_for_resources(
        ResourceRepositoryInterface $repository,
        RequestConfiguration        $configuration
    ) {
        $configuration->getRepositoryMethod('findBy')->willReturn('findAll');
        $configuration->getRepositoryArguments(array())->willReturn(array(5));

        $repository->findAll(5)->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->getResource($configuration, 'findBy')->shouldReturn(array('foo', 'bar'));
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_creating_resource(
        ResourceFactoryInterface $factory,
        RequestConfiguration     $configuration
    ) {
        $configuration->getFactoryMethod('createNew')->willReturn('createNew');
        $configuration->getFactoryArguments(array())->willReturn(array('00032'));

        $factory->createNew('00032')->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->createResource($configuration, 'createNew')->shouldReturn(array('foo', 'bar'));
    }
}
