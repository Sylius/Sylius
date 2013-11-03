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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Sylius resource twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class SyliusResourceExtension extends Twig_Extension
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
     * @var array
     */
    private $syliusRouteParams = array();

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, $paginateTemplate, $sortingTemplate)
    {
        $this->container = $container;
        $this->paginateTemplate = $paginateTemplate;
        $this->sortingTemplate = $sortingTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_resource_sort' => new Twig_Function_Method($this, 'renderSortingLink', array('is_safe' => array('html'))),
            'sylius_resource_paginate' => new Twig_Function_Method($this, 'renderPaginateSelect', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function fetchRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $this->request = $event->getRequest();

        $routeParams = $this->request->attributes->get('_route_params', array());
        if (array_key_exists('_sylius', $routeParams)) {
            $this->syliusRouteParams = $routeParams['_sylius'];
        }
    }

    /**
     * @param string$property
     * @param mixed $label
     * @param string $order
     * @param array $options
     * @return string
     */
    public function renderSortingLink($property, $label = null, $order = 'asc', array $options = array())
    {
        if (array_key_exists('sortable', $this->syliusRouteParams) &&
            !$this->syliusRouteParams['sortable']) {
            return $label;
        }

        if ('asc' !== $order && 'desc' !== $order) {
            $order ='asc';
        }

        $options = $this->getOptions($options, $this->sortingTemplate);
        $label = null === $label ? $property : $label;
        $sorting = $this->request->get('sorting', array());
        $currentOrder = isset($sorting[$property]) ? $sorting[$property] : null;

        if (null !== $currentOrder) {
            $order = 'asc' === $currentOrder ? 'desc' : 'asc';
        }

        $url = $this->container->get('router')->generate(
            $this->getRouteName($options['route']),
            $this->getRouteParams(
                array('sorting' => array($property => $order)),
                $options['route_params']
            )
        );

        return $this->container->get('templating')->render(
            $options['template'],
            array(
                'url' => $url,
                'label' => $label,
                'icon' => $property == key($sorting),
                'currentOrder' => $currentOrder
            )
        );
    }

    /**
     * @param \Pagerfanta\Pagerfanta $paginator
     * @param array $limitOptions
     * @param array $options
     * @return string
     */
    public function renderPaginateSelect(Pagerfanta $paginator, array $limitOptions, array $options = array())
    {
        if (array_key_exists('paginate', $this->syliusRouteParams) &&
            is_string($this->syliusRouteParams['paginate'])) {
            $options = $this->getOptions($options, $this->paginateTemplate);
            $paginateName = substr($this->syliusRouteParams['paginate'], 1);

            foreach ($limitOptions as $limit) {
                $routeParams = $this->getRouteParams(
                    array($paginateName => $limit),
                    $options['route_params']
                );

                if (array_key_exists('page', $routeParams)) {
                    $routeParams['page'] = 1;
                }

                $limits[$limit] = $this->container->get('router')->generate(
                    $this->getRouteName($options['route']),
                    $routeParams
                );
            }

            return $this->container->get('templating')->render(
                $options['template'],
                array(
                    'paginator' => $paginator,
                    'limits' => $limits
                )
            );
        }
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
     * @param null $route
     * @return mixed|null
     */
    private function getRouteName($route = null)
    {
        return (null === $route) ? $this->request->attributes->get('_route') : $route;
    }

    /**
     * @param array $options
     * @param $defaultTemplate
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
}
