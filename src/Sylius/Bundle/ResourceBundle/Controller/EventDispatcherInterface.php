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

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface EventDispatcherInterface
{
    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatch($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPreEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);

    /**
     * @param string $eventName
     * @param RequestConfiguration $requestConfiguration
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource);
}
