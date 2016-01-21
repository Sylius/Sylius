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
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RedirectHandler implements RedirectHandlerInterface
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
     * {@inheritdoc}
     */
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource)
    {
        $routes = $this->router->getRouteCollection();
        $redirectRouteName = $configuration->getRedirectRoute(ResourceActions::SHOW);

        if (null === $routes->get($redirectRouteName)) {
            $redirectRouteName = $configuration->getRedirectRoute(ResourceActions::INDEX);
        }

        return $this->redirectToRoute(
            $configuration,
            $redirectRouteName,
            $configuration->getRedirectParameters($resource)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToIndex(RequestConfiguration $configuration, ResourceInterface $resource = null)
    {
        return $this->redirectToRoute(
            $configuration,
            $configuration->getRedirectRoute('index'),
            $configuration->getRedirectParameters()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToRoute(RequestConfiguration $configuration, $route, array $parameters = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer($configuration);
        }

        return $this->redirect($configuration, $this->router->generate($route, $parameters));
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function redirectToReferer(RequestConfiguration $configuration)
    {
        return $this->redirect($configuration, $configuration->getRedirectReferer());
    }
}
