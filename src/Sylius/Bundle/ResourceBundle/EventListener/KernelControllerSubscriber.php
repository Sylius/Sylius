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
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Doctrine listener used to set the request on the configurable controllers.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 *
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

    public function __construct(ParametersParser $parametersParser, Parameters $parameters, array $settings)
    {
        $this->parametersParser = $parametersParser;
        $this->parameters = $parameters;
        $this->settings = $settings;
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

            $parameters = $request->attributes->get('_sylius', array());
            $parameters = array_merge($this->settings, $parameters);
            $parameters = $this->parametersParser->parse($parameters, $request);

            $this->parameters->replace($parameters);

            $controller[0]->getConfiguration()->setRequest($request);
            $controller[0]->getConfiguration()->setParameters($this->parameters);
        }
    }
}
