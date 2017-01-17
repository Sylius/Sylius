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
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param SymfonyEventDispatcherInterface $eventDispatcher
     */
    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPreEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.pre_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatchPostEvent($eventName, RequestConfiguration $requestConfiguration, ResourceInterface $resource)
    {
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();
        $event = $this->getEvent($resource);

        $this->eventDispatcher->dispatch(sprintf('%s.%s.post_%s', $metadata->getApplicationName(), $metadata->getName(), $eventName), $event);

        return $event;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return ResourceControllerEvent
     */
    private function getEvent(ResourceInterface $resource)
    {
        return new ResourceControllerEvent($resource);
    }
}
