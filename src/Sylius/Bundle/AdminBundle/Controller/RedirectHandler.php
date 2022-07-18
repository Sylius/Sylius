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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\AdminBundle\Storage\FilterStorageInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\Response;

final class RedirectHandler implements RedirectHandlerInterface
{
    public function __construct(
        private RedirectHandlerInterface $decoratedRedirectHandler,
        private FilterStorageInterface $filterStorage
    ) {
    }

    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource): Response
    {
        return $this->decoratedRedirectHandler->redirectToResource($configuration, $resource);
    }

    public function redirectToIndex(RequestConfiguration $configuration, ?ResourceInterface $resource = null): Response
    {
        $request = $configuration->getRequest();
        $request->query->add($this->filterStorage->all());

        $requestConfiguration = new RequestConfiguration(
            $configuration->getMetadata(),
            $request,
            $configuration->getParameters()
        );

        return $this->decoratedRedirectHandler->redirectToReferer($requestConfiguration);
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
