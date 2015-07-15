<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Twig;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RouterInterface;

/**
 * Sylius resource twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ResourceExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $paginateTemplate;

    /**
     * @var string
     */
    private $sortingTemplate;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Constructor.
     *
     * @param RouterInterface $router
     * @param Parameters      $parameters
     * @param string          $paginateTemplate
     * @param string          $sortingTemplate
     */
    public function __construct(RouterInterface $router, Parameters $parameters, $paginateTemplate, $sortingTemplate)
    {
        $this->router = $router;
        $this->paginateTemplate = $paginateTemplate;
        $this->sortingTemplate = $sortingTemplate;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
             new \Twig_SimpleFunction(
                 'sylius_resource_sort',
                 array($this, 'renderSortingLink'),
                 array('needs_environment' => true, 'is_safe' => array('html'))
             ),
             new \Twig_SimpleFunction(
                 'sylius_resource_paginate',
                 array($this, 'renderPaginateSelect'),
                 array('needs_environment' => true, 'is_safe' => array('html'))
             ),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function fetchRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $this->request = $event->getRequest();
    }

    /**
     * @param \Twig_Environment $twig
     * @param string            $property
     * @param null|string       $label
     * @param string            $order
     * @param array             $options
     *
     * @return string
     */
    public function renderSortingLink(\Twig_Environment $twig, $property, $label = null, $order = 'asc', array $options = array())
    {
        if (null === $label) {
            $label = $property;
        }

        if (false === $this->parameters->get('sortable')) {
            return $label;
        }

        if ('asc' !== $order && 'desc' !== $order) {
            $order = 'asc';
        }

        $options = $this->getOptions($options, $this->sortingTemplate);
        $sorting = $this->request->query->get($this->getParameterName('sorting'), $this->parameters->get($this->getParameterName('sorting'), array('id' => 'asc')));
        $currentOrder = null;

        if (isset($sorting[$property])) {
            $currentOrder = $sorting[$property];
            $order        = 'asc' === $sorting[$property] ? 'desc' : 'asc';
        }

        $url = $this->router->generate(
            $this->getRouteName($options['route']),
            $this->getRouteParams(
                array($this->getParameterName('sorting') => array($property => $order)),
                $options['route_params']
            )
        );

        return $twig->render($options['template'], array(
            'url'          => $url,
            'label'        => $label,
            'icon'         => $property == key($sorting),
            'currentOrder' => $currentOrder,
        ));
    }

    /**
     * @param \Twig_Environment $twig
     * @param Pagerfanta        $paginator
     * @param array             $limitOptions
     * @param array             $options
     *
     * @return string
     */
    public function renderPaginateSelect(\Twig_Environment $twig, Pagerfanta $paginator, array $limitOptions = array(), array $options = array())
    {
        $parameterName = $this->parameters->get('parameter_name');
        if (false !== $this->parameters->get('paginate') &&
            isset($parameterName['paginate'])) {
            $options = $this->getOptions($options, $this->paginateTemplate);
            $paginateName = $this->getParameterName('paginate');
            $limitOptions = !empty($limitOptions) ? $limitOptions : $this->parameters->get('allowed_paginate');

            $limits = array();
            foreach ($limitOptions as $limit) {
                $routeParams = $this->getRouteParams(
                    array($paginateName => $limit),
                    $options['route_params']
                );

                if (array_key_exists('page', $routeParams)) {
                    $routeParams['page'] = 1;
                }

                $limits[$limit] = $this->router->generate(
                    $this->getRouteName($options['route']),
                    $routeParams
                );
            }

            return $twig->render($options['template'], array(
                'paginator' => $paginator,
                'limits'    => $limits,
            ));
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_resource';
    }

    /**
     * @param array $params
     * @param array $default
     *
     * @return array
     */
    private function getRouteParams(array $params = array(), array $default = array())
    {
        return array_merge(
            $this->request->query->all(),
            $this->request->attributes->get('_route_params'),
            array_merge($params, $default)
        );
    }

    /**
     * @param null|string $route
     *
     * @return mixed|null
     */
    private function getRouteName($route = null)
    {
        return null === $route ? $this->request->attributes->get('_route') : $route;
    }

    /**
     * @param array  $options
     * @param string $defaultTemplate
     *
     * @return array
     */
    private function getOptions(array $options, $defaultTemplate)
    {
        if (!array_key_exists('template', $options)) {
            $options['template'] = $defaultTemplate;
        }

        if (!array_key_exists('route', $options)) {
            $options['route'] = null;
        }

        if (!array_key_exists('route_params', $options)) {
            $options['route_params'] = array();
        }

        return $options;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getParameterName($key)
    {
        $parameterName = $this->parameters->get('parameter_name');
        if (isset($parameterName[$key]) && !is_array($parameterName[$key])) {
            return $parameterName[$key];
        }

        return $key;
    }
}
