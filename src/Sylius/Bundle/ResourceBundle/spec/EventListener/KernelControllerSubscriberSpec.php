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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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

        $event->getController()->willReturn([$resourceController]);
        $event->getRequest()->willReturn($request);

        $request->attributes = $parameterBag;
        $request->headers    = $headerBag;

        $this->beConstructedWith(
            $parametersParser,
            $parameters,
            [
                'paginate' => false,
                'limit' => false,
                'sortable' => false,
                'sorting' => null,
                'filterable' => false,
                'criteria' => null,
            ]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\EventListener\KernelControllerSubscriber');
    }

    function it_is_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_events()
    {
        $this::getSubscribedEvents([
            'kernel.controller' => ['onKernelController', 0]
        ]);
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

        $parameterBag->get('_sylius', [])->willReturn([]);

        $request->get('criteria')->willReturn(['product' => 10]);
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            [
                'paginate' => false,
                'limit' => false,
                'sortable' => false,
                'filterable' => false,
                'sorting' => null,
                'criteria' => null,
            ],
            $request
        )->willReturn([[], []]);

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', [])->willReturn([]);

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

        $parameterBag->get('_sylius', [])->willReturn([
            'paginate' => 20,
            'filterable' => true,
            'sorting' => '$sorting',
            'sortable' => true,
            'criteria' => '$c'
        ]);

        $request->get('criteria')->willReturn(['product' => 10]);
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            [
                'paginate' => 20,
                'limit' => false,
                'sortable' => true,
                'sorting' => '$sorting',
                'filterable' => true,
                'criteria' => '$c',
            ],
            $request
        )->willReturn([[], []]);

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', [])->willReturn([]);

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

        $parameterBag->get('_sylius', [])->willReturn([
            'paginate' => 20,
            'filterable' => true,
            'sorting' => '$sorting',
            'sortable' => true,
            'criteria' => '$c'
        ]);

        $request->get('criteria')->willReturn(['product' => 10]);
        $request->get('paginate')->willReturn(10);

        $parametersParser->parse(
            [
                'serialization_version' => '1.0.1',
                'serialization_groups' => ['Default', 'Details'],
                'paginate' => 20,
                'limit' => false,
                'sortable' => true,
                'sorting' => '$sorting',
                'filterable' => true,
                'criteria' => '$c',
            ],
            $request
        )->willReturn([[], []]);

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('parameter_name', Argument::type('array'))->shouldBeCalled();

        $parameterBag->get('_route_params', [])->willReturn([]);

        $this->onKernelController($event);
    }
}
