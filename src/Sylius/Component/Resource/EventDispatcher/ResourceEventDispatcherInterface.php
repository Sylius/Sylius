<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\EventDispatcher;

use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;

interface ResourceEventDispatcherInterface
{
    /**
     * Dispatch a resource event.
     *
     * @param string            $name
     * @param ResourceInterface $resource
     *
     * @return ResourceEvent
     */
    public function dispatchResourceEvent($name, ResourceInterface $resource);

    /**
     * Dispatch raw event with full name.
     *
     * @param $name
     * @param Event $event
     */
    public function dispatch($name, Event $event);
}
