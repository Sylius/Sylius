<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirects helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RedirectHandler
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(Configuration $config, RouterInterface $router)
    {
        $this->router = $router;
        $this->config = $config;
    }

    /**
     * @param object $resource
     *
     * @return RedirectResponse
     */
    public function redirectTo($resource)
    {
        $parameters = $this->config->getRedirectParameters($resource);

        $routes = $this->router->getRouteCollection();
        $route = $this->config->getRedirectRoute('show');

        if (!$routes->get($route)) {
            $route = $this->config->getRedirectRoute('index');
        }

        return $this->redirectToRoute($route, $parameters);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToIndex()
    {
        return $this->redirectToRoute($this->config->getRedirectRoute('index'), $this->config->getRedirectParameters());
    }

    /**
     * @param string $route
     * @param array  $data
     *
     * @return RedirectResponse
     */
    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->redirect($this->router->generate($route, $data));
    }

    /**
     * @param string  $url
     * @param integer $status
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        if ($this->config->isHeaderRedirection()) {
            return new Response('', 200, array(
                'X-SYLIUS-LOCATION' => $url.$this->config->getRedirectHash(),
            ));
        }

        return new RedirectResponse($url.$this->config->getRedirectHash(), $status);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToReferer()
    {
        return $this->redirect($this->config->getRedirectReferer());
    }
}
