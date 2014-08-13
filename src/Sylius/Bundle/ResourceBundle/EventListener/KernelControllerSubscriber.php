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
 * Doctrine listener used to set the request on the configurable controllers.
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
    private $apiGroupsRegexp  = '/(g|groups)=(?P<groups>[a-z,]+)/i';

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
            'kernel.controller' => array('onKernelController', 0)
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

        if ($controller[0] instanceof ResourceController) {
            $request = $event->getRequest();

            $parameters = $this->parseApiData($request);
            $parameters = array_merge($this->settings, $parameters);
            $parameters = $this->parametersParser->parse($parameters, $request);

            $this->parameters->replace($parameters);

            $controller[0]->getConfiguration()->setRequest($request);
            $controller[0]->getConfiguration()->setParameters($this->parameters);
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
                $data['serialization_groups'] = explode(',', $matches['groups']);
            }
        }

        return array_merge($request->attributes->get('_sylius', array()), $data);
    }
}
