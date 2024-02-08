<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;

final class RedirectHandler implements RedirectHandlerInterface
{
    public function __construct(
        private RedirectHandlerInterface $decoratedRedirectHandler,
        private FilterStorageInterface $filterStorage,
    ) {
    }

    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource): Response
    {
        return $this->decoratedRedirectHandler->redirectToResource($configuration, $resource);
    }

    public function redirectToIndex(RequestConfiguration $configuration, ?ResourceInterface $resource = null): Response
    {
        return $this->decoratedRedirectHandler->redirectToRoute(
            $configuration,
            (string) $configuration->getRedirectRoute('index'),
            array_merge($configuration->getRedirectParameters($resource), $this->filterStorage->all()),
        );
    }

    public function redirectToRoute(RequestConfiguration $configuration, string $route, array $parameters = []): Response
    {
        return $this->decoratedRedirectHandler->redirectToRoute($configuration, $route, $parameters);
    }

    public function redirect(RequestConfiguration $configuration, string $url, int $status = 302): Response
    {
        return $this->decoratedRedirectHandler->redirect($configuration, $url, $status);
    }

    public function redirectToReferer(RequestConfiguration $configuration): Response
    {
        return $this->decoratedRedirectHandler->redirectToReferer($configuration);
    }
}
