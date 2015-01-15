<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class KernelControllerSubscriberSpec extends ObjectBehavior
{
    function let(
        ParametersParser $parametersParser,
        Parameters $parameters,
        Request $request,
        ParameterBag $parameterBag,
        HeaderBag $headerBag,
        FilterControllerEvent $event,
        ResourceController $resourceController,
        Configuration $configuration
    )
    {
        $resourceController->getConfiguration()->willReturn($configuration);

        $event->getController()->willReturn(array($resourceController));
        $event->getRequest()->willReturn($request);

        $request->attributes = $parameterBag;
        $request->headers    = $headerBag;

        $this->beConstructedWith(
            $parametersParser,
            $parameters,
            array(
                'paginate' => false,
                'limit' => false,
                'sortable' => false,
                'sorting' => null,
                'filterable' => false,
                'criteria' => null,
            )
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\EventListener\KernelControllerSubscriber');
    }

    function it_is_event_subscriber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_events()
    {
        $this::getSubscribedEvents(array(
            'kernel.controller' => array('onKernelController', 0)
        ));
    }

    function it_should_parse_empty_request(
        $event,
        $parametersParser,
        $parameters,
        $request,
        $parameterBag,
        $headerBag
    ) {
        $headerBag->has('Accept')->willReturn(false);

        $parameterBag->get('_sylius', array())->willReturn(array());

        $request->get('criteria')->willReturn(array('product' => 10));
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            array(
                'paginate' => false,
                'limit' => false,
                'sortable' => false,
                'filterable' => false,
                'sorting' => null,
                'criteria' => null,
            ),
            $request
        )->willReturn(array(array(), array()));

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', array())->willReturn(array());

        $this->onKernelController($event);
    }

    function it_should_parse_request(
        $event,
        $parametersParser,
        $parameters,
        $request,
        $parameterBag,
        $headerBag
    ) {
        $headerBag->has('Accept')->willReturn(false);

        $parameterBag->get('_sylius', array())->willReturn(array(
            'paginate' => 20,
            'filterable' => true,
            'sorting' => '$sorting',
            'sortable' => true,
            'criteria' => '$c'
        ));

        $request->get('criteria')->willReturn(array('product' => 10));
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            array(
                'paginate' => 20,
                'limit' => false,
                'sortable' => true,
                'sorting' => '$sorting',
                'filterable' => true,
                'criteria' => '$c',
            ),
            $request
        )->willReturn(array(array(), array()));

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', array())->willReturn(array());

        $this->onKernelController($event);
    }

    function it_should_parse_request_and_headers(
        $event,
        $parametersParser,
        $parameters,
        $request,
        $parameterBag,
        $headerBag
    ) {
        $headerBag->has('Accept')->willReturn(true);
        $headerBag->get('Accept')->willReturn('Accept: application/json; version=1.0.1; groups=Default,Details');

        $parameterBag->get('_sylius', array())->willReturn(array(
            'paginate' => 20,
            'filterable' => true,
            'sorting' => '$sorting',
            'sortable' => true,
            'criteria' => '$c'
        ));

        $request->get('criteria')->willReturn(array('product' => 10));
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            array(
                'serialization_version' => '1.0.1',
                'serialization_groups' => array('Default', 'Details'),
                'paginate' => 20,
                'limit' => false,
                'sortable' => true,
                'sorting' => '$sorting',
                'filterable' => true,
                'criteria' => '$c',
            ),
            $request
        )->willReturn(array(array(), array()));

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', array())->willReturn(array());

        $this->onKernelController($event);
    }
}
