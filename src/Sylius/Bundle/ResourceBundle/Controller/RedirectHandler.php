<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RedirectHandler implements RedirectHandlerInterface
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
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource): Response
    {
        try {
            return $this->redirectToRoute(
                $configuration,
                $configuration->getRedirectRoute(ResourceActions::SHOW),
                $configuration->getRedirectParameters($resource)
            );
        } catch (RouteNotFoundException $exception) {
            return $this->redirectToRoute(
                $configuration,
                $configuration->getRedirectRoute(ResourceActions::INDEX),
                $configuration->getRedirectParameters($resource)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToIndex(RequestConfiguration $configuration, ?ResourceInterface $resource = null): Response
    {
        return $this->redirectToRoute(
            $configuration,
            $configuration->getRedirectRoute('index'),
            $configuration->getRedirectParameters($resource)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToRoute(RequestConfiguration $configuration, string $route, array $parameters = []): Response
    {
        if ('referer' === $route) {
            return $this->redirectToReferer($configuration);
        }

        return $this->redirect($configuration, $this->router->generate($route, $parameters));
    }

    /**
     * {@inheritdoc}
     */
    public function redirect(RequestConfiguration $configuration, string $url, int $status = 302): Response
    {
        if ($configuration->isHeaderRedirection()) {
            return new Response('', 200, [
                'X-SYLIUS-LOCATION' => $url . $configuration->getRedirectHash(),
            ]);
        }

        return new RedirectResponse($url . $configuration->getRedirectHash(), $status);
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToReferer(RequestConfiguration $configuration): Response
    {
        return $this->redirect($configuration, $configuration->getRedirectReferer());
    }
}
