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
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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
        FilterControllerEvent $event,
        ResourceController $resourceController,
        Configuration $configuration
    )
    {
        $resourceController->getConfiguration()->willReturn($configuration);

        $event->getController()->willreturn(array($resourceController));
        $event->getRequest()->willReturn($request);

        $request->attributes = $parameterBag;
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

    function it_is_event_suvscriver()
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
        FilterControllerEvent $event,
        ParametersParser $parametersParser,
        Parameters $parameters,
        Request $request,
        ParameterBag $parameterBag,
        ResourceController $resourceController
    ) {
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
        )->shouldBeCalled()->willReturn(array(array(), array()));

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('paramater_name', Argument::type('array'))->shouldBeCalled();

        $this->onKernelController($event);
    }

    function it_should_parse_request(
        FilterControllerEvent $event,
        ParametersParser $parametersParser,
        Parameters $parameters,
        Request $request,
        ParameterBag $parameterBag
    ) {
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
        )->shouldBeCalled()->willReturn(array(array(), array()));

        $parameters->replace(Argument::type('array'))->shouldBeCalled();
        $parameters->set('paramater_name', Argument::type('array'))->shouldBeCalled();

        $this->onKernelController($event);
    }
}
