<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Kernel listener used to set the request on the configurable controllers.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class KernelControllerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParametersParser
     */
    private $parametersParser;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @var array
     */
    private $settings;

    private $forceApiVersion = false;

    private $apiVersionHeader = 'Accept';
    private $apiGroupsHeader  = 'Accept';

    private $apiVersionRegexp = '/(v|version)=(?P<version>[0-9\.]+)/i';
    private $apiGroupsRegexp  = '/(g|groups)=(?P<groups>[a-z,_\s]+)/i';

    public function __construct(ParametersParser $parametersParser, Parameters $parameters, array $settings, $forceApiVersion = false)
    {
        $this->parametersParser = $parametersParser;
        $this->parameters = $parameters;
        $this->settings = $settings;
        $this->forceApiVersion = $forceApiVersion;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.controller' => array('onKernelController', 0),
        );
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controller = reset($controller);
        if ($controller instanceof ResourceController) {
            $this->processRequest($controller, $event->getRequest());
        }
    }

    /**
     * @param ResourceController $controller
     * @param Request            $request
     */
    private function processRequest(ResourceController $controller, Request $request)
    {
        $parameters = array_merge($this->settings, $this->parseApiData($request));
        list($parameters, $parameterNames) = $this->parametersParser->parse($parameters, $request);

        $this->parameters->replace($parameters);
        $this->parameters->set('parameter_name', $parameterNames);

        $controller->getConfiguration()->setRequest($request);
        $controller->getConfiguration()->setParameters($this->parameters);

        $routeParams = $request->attributes->get('_route_params', array());
        if (isset($routeParams['_sylius'])) {
            unset($routeParams['_sylius']);

            $request->attributes->set('_route_params', $routeParams);
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function parseApiData(Request $request)
    {
        $data = array();
        if ($request->headers->has($this->apiVersionHeader)) {
            if (preg_match($this->apiVersionRegexp, $request->headers->get($this->apiVersionHeader), $matches)) {
                $data['serialization_version'] = $matches['version'];
            } elseif ($this->forceApiVersion) {
                $data['serialization_version'] = '1.0';
            }
        } elseif ($this->forceApiVersion) {
            $data['serialization_version'] = '1.0';
        }

        if ($request->headers->has($this->apiGroupsHeader)) {
            if (preg_match($this->apiGroupsRegexp, $request->headers->get($this->apiGroupsHeader), $matches)) {
                $data['serialization_groups'] = array_map('trim', explode(',', $matches['groups']));
            }
        }

        return array_merge($request->attributes->get('_sylius', array()), $data);
    }
}
