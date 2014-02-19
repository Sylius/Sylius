<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Resource resolver spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
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

    function it_calls_proper_method_with_arguments_based_on_configuration(
        RepositoryInterface $repository,
        $configuration
    ) {
        $configuration->getMethod('findBy')->willReturn('findAll');
        $configuration->getArguments(array())->willReturn(array(5));

        $repository->findAll(5)->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->getResource($repository, 'findBy')->shouldReturn(array('foo', 'bar'));
    }
}
