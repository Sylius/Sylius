<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Resource resolver spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceResolver extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceResolver');
    }

    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $repository
     * @param Sylius\Bundle\ResourceBundle\Controller\Configuration  $configuration
     */
    function it_calls_proper_method_with_arguments_based_on_configuration($repository, $configuration)
    {
        $configuration->getMethod('findBy')->willReturn('findLatest');
        $configuration->getArguments(array())->willReturn(array(5));

        $repository->findLatest(5)->shouldBeCalled()->willReturn(array('foo', 'bar'));

        $this->getResource($repository, $configuration, 'findBy')->shouldReturn(array('foo', 'bar'));
    }
}
