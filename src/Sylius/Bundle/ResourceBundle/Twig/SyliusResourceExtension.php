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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Sylius resource twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class SyliusResourceExtension extends \Twig_Extension
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
     * @var Request
     */
    private $request;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param string             $paginateTemplate
     * @param string             $sortingTemplate
     */
    public function __construct(ContainerInterface $container, $paginateTemplate, $sortingTemplate)
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
             new \Twig_SimpleFunction('sylius_resource_sort', array($this, 'renderSortingLink'), array('is_safe' => array('html'))),
             new \Twig_SimpleFunction('sylius_resource_paginate', array($this, 'renderPaginateSelect'), array('is_safe' => array('html'))),
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
     * @param string      $property
     * @param null|string $label
     * @param string      $order
     * @param array       $options
     *
     * @return string
     */
    public function renderSortingLink($property, $label = null, $order = 'asc', array $options = array())
    {
        if (array_key_exists('sortable', $this->syliusRouteParams) && !$this->syliusRouteParams['sortable']) {
            return $label;
        }

        if ('asc' !== $order && 'desc' !== $order) {
            $order = 'asc';
        }

        $options = $this->getOptions($options, $this->sortingTemplate);
        $sorting = $this->request->get('sorting');
        if (null !== $sorting) {
            if (isset($sorting[$property])) {
                $currentOrder = $sorting[$property];
                $order        = 'asc' === $sorting[$property] ? 'desc' : 'asc';
            } else {
                $currentOrder = null;
            }
        } else {
            $currentOrder = null;
            $sorting      = isset($this->syliusRouteParams['sorting']) ? $this->syliusRouteParams['sorting'] : array('id' => 'asc');
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
                'url'          => $url,
                'label'        => null === $label ? $property : $label,
                'icon'         => $property == key($sorting),
                'currentOrder' => $currentOrder,
            )
        );
    }

    /**
     * @param Pagerfanta $paginator
     * @param array      $limitOptions
     * @param array      $options
     *
     * @return string
     */
    public function renderPaginateSelect(Pagerfanta $paginator, array $limitOptions, array $options = array())
    {
        if (array_key_exists('paginate', $this->syliusRouteParams) &&
            is_string($this->syliusRouteParams['paginate'])) {
            $options = $this->getOptions($options, $this->paginateTemplate);
            $paginateName = substr($this->syliusRouteParams['paginate'], 1);

            $limits = array();
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
                    'limits' => $limits,
                )
            );
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
}
