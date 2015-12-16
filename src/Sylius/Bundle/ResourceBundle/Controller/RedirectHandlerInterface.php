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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface RedirectHandlerInterface
{
    /**
     * @param RequestConfiguration $configuration
     * @param ResourceInterface $resource
     *
     * @return RedirectResponse
     */
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource);

    /**
     * @param RequestConfiguration $configuration
     * @param null|ResourceInterface $resource
     *
     * @return RedirectResponse
     */
    public function redirectToIndex(RequestConfiguration $configuration, ResourceInterface $resource = null);

    /**
     * @param RequestConfiguration $configuration
     * @param string               $route
     * @param array                $parameters
     *
     * @return RedirectResponse
     */
    public function redirectToRoute(RequestConfiguration $configuration, $route, array $parameters = array());


    /**
     * @param RequestConfiguration $configuration
     * @param $url
     * @param int $status
     *
     * @return RedirectResponse
     */
    public function redirect(RequestConfiguration $configuration, $url, $status = 302);

    /**
     * @param RequestConfiguration $configuration
     *
     * @return RedirectResponse
     */
    public function redirectToReferer(RequestConfiguration $configuration);
}
