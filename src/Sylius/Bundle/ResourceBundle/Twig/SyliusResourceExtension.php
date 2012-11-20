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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Sylius resource twig helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusResourceExtension extends Twig_Extension
{
    private $request;
    private $router;

    public function __construct(ContainerInterface $container, RouterInterface $router)
    {
        $this->request = $container->get('request');
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

    public function renderSortingLink($property, $label = null, $order = null, $route = null)
    {
        $label = null === $label ? $property : $label;
        $route = null === $route ? $this->request->attributes->get('_route') : $route;

        $sorting = $this->request->get('sorting');

        if (null === $order && isset($sorting[$property])) {
            $currentOrder = $sorting[$property];

            $order = 'asc' === $currentOrder ? 'desc' : 'asc';
        }

        $order = null === $order ? 'asc' : $order;

        $url = $this->router->generate($route, array(
            'sorting' => array($property => $order)
        ));

        return sprintf('<a href="%s">%s</a>', $url, $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_resource';
    }
}
