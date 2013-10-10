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
 */
class SyliusResourceExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_resource_sort' => new Twig_Function_Method($this, 'renderSortingLink', array('is_safe' => array('html'))),
        );
    }

    public function fetchRequest(GetResponseEvent $event)
    {
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        $this->request = $event->getRequest();
    }

    public function renderSortingLink($property, $label = null, $order = null, $route = null, array $routeParameters = array())
    {
        $label = null === $label ? $property : $label;
        $route = null === $route ? $this->request->attributes->get('_route') : $route;

        $routeParameters = empty($routeParameters) ? $this->request->attributes->get('_route_parameters', array()) : $routeParameters;

        $sorting = $this->request->get('sorting', array());

        if (null === $order && isset($sorting[$property])) {
            $currentOrder = $sorting[$property];

            $order = 'asc' === $currentOrder ? 'desc' : 'asc';
        }

        $order = null === $order ? 'asc' : $order;

        $url = $this->router->generate($route, array_merge(
            array('sorting' => array($property => $order)), $routeParameters
        ));

        // @TODO: Move this to templates when we refactor resource bundle.
        $active = $property == key($sorting) ? ($currentOrder === 'desc' ? ' <i class="icon icon-chevron-down"></i>' : ' <i class="icon icon-chevron-up"></i>'): '';

        return sprintf('<a href="%s">%s%s</a>', $url, $label, $active);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_resource';
    }
}
