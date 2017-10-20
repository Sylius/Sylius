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
use Symfony\Component\HttpFoundation\Response;

interface RedirectHandlerInterface
{
    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     *
     * @return Response
     */
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource): Response;

    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface|null $resource
     *
     * @return Response
     */
    public function redirectToIndex(RequestConfiguration $configuration, ?ResourceInterface $resource = null): Response;

    /**
     * @param RequestConfiguration $configuration
     * @param string               $route
     * @param array                $parameters
     *
     * @return Response
     */
    public function redirectToRoute(RequestConfiguration $configuration, string $route, array $parameters = []): Response;

    /**
     * @param RequestConfiguration $configuration
     * @param string $url
     * @param int $status
     *
     * @return Response
     */
    public function redirect(RequestConfiguration $configuration, string $url, int $status = 302): Response;

    /**
     * @param RequestConfiguration $configuration
     *
     * @return Response
     */
    public function redirectToReferer(RequestConfiguration $configuration): Response;
}
