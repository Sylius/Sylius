<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Resource resolver spec.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourceResolverSpec extends ObjectBehavior
{
    function let(Configuration $configuration)
    {
        $this->beConstructedWith($configuration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceResolver');
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_getting_resource(
        RepositoryInterface $repository,
        $configuration
    ) {
        $configuration->getRepositoryMethod('findBy')->willReturn('findAll');
        $configuration->getRepositoryArguments([])->willReturn([5]);

        $repository->findAll(5)->willReturn(['foo', 'bar']);

        $this->getResource($repository, 'findBy')->shouldReturn(['foo', 'bar']);
    }

    function it_calls_proper_method_with_arguments_based_on_configuration_when_creating_resource(
        FactoryInterface $factory,
        $configuration
    ) {
        $configuration->getFactoryMethod('createNew')->willReturn('createNew');
        $configuration->getFactoryArguments([])->willReturn([]);

        $factory->createNew()->willReturn(['foo', 'bar']);

        $this->createResource($factory, 'createNew')->shouldReturn(['foo', 'bar']);
    }
}
