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

use Sylius\Component\Resource\Model\ResourceInterface;
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
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     *
     * @return RedirectResponse
     */
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource)
    {
        return $this->redirectToRoute(
            $configuration,
            $configuration->getRedirectRoute('show'),
            $configuration->getRedirectParameters($resource)
        );
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return RedirectResponse
     */
    public function redirectToIndex(RequestConfiguration $configuration)
    {
        return $this->redirectToRoute(
            $configuration,
            $configuration->getRedirectRoute('index'),
            $configuration->getRedirectParameters()
        );
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string               $route
     * @param array                $parameters
     *
     * @return RedirectResponse
     */
    public function redirectToRoute(RequestConfiguration $configuration, $route, array $parameters = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer($configuration);
        }

        return $this->redirect($configuration, $this->router->generate($route, $parameters));
    }

    /**
     * @param RequestConfiguration $configuration
     * @param $url
     * @param int $status
     *
     * @return RedirectResponse
     */
    public function redirect(RequestConfiguration $configuration, $url, $status = 302)
    {
        if ($configuration->isHeaderRedirection()) {
            return new Response('', 200, array(
                'X-SYLIUS-LOCATION' => $url.$configuration->getRedirectHash(),
            ));
        }

        return new RedirectResponse($url.$configuration->getRedirectHash(), $status);
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return RedirectResponse
     */
    public function redirectToReferer(RequestConfiguration $configuration)
    {
        return $this->redirect($configuration, $configuration->getRedirectReferer());
    }
}
