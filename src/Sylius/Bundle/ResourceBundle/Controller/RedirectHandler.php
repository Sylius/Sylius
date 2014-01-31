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
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirects helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RedirectHandler
{
    private $router;
    private $config;

    public function __construct(Configuration $config, RouterInterface $router)
    {
        $this->router = $router;
        $this->config = $config;
    }

    public function redirectTo($resource)
    {
        $parameters = $this->config->getRedirectParameters($resource);

        return $this->redirectToRoute(
            $this->config->getRedirectRoute('show'),
            $parameters
        );
    }

    public function redirectToIndex()
    {
        return $this->redirectToRoute($this->config->getRedirectRoute('index'), $this->config->getRedirectParameters());
    }

    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->redirect($this->router->generate($route, $data));
    }

    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    protected function redirectToReferer()
    {
        return $this->redirect($this->config->getRequest()->headers->get('referer'));
    }
}
