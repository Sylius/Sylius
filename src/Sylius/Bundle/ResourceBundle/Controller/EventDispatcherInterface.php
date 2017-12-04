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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;

interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatch(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param mixed $resources
     *
     * @return ResourceControllerEvent
     */
    public function dispatchMultiple(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        $resources
    ): ResourceControllerEvent;

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPreEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPostEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchInitializeEvent(
        string $eventName,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource
    ): ResourceControllerEvent;
}
