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
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ResourceEventDispatcher implements ResourceEventDispatcherInterface
{
    /**
     * @var ResourceMetadataInterface
     */
    private $metadata;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param ResourceMetadataInterface $metadata
     * @param EventDispatcherInterface  $dispatcher
     */
    public function __construct(ResourceMetadataInterface $metadata, EventDispatcherInterface $dispatcher)
    {
        $this->metadata = $metadata;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchResourceEvent($name, ResourceInterface $resource)
    {
        $eventName = $this->getEventName($name);

        $event = $this->createEvent($resource);
        $this->dispatcher->dispatch($eventName, $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($name, Event $event)
    {
        return $this->dispatcher->dispatch($name, $event);
    }

    /**
     * Create the full event name.
     *
     * @param string $name
     *
     * @return string
     */
    private function getEventName($name)
    {
        return sprintf('%s.%s.%s', $this->metadata->getApplicationName(), $this->metadata->getResourceName(), $name);
    }

    /**
     * Create a resource event object.
     *
     * @param ResourceInterface $resource
     *
     * @return ResourceEvent
     */
    private function createEvent(ResourceInterface $resource)
    {
        return new ResourceEvent($resource, $this->metadata);
    }
}
