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
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\ResourceControllerEvents;
use Sylius\Component\Resource\ResourceActions;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Daniel Leech <daniel@dantleech.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    const PRE_PREFIX = 'pre_';
    const POST_PREFIX = 'post_';
    const NO_PREFIX = '';

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
        $event = new ResourceControllerEvent($resource, $requestConfiguration);
        $this->eventDispatcher->dispatch($eventName, $event);
        $this->dispatchResourceEvent($eventName, $event);

        return $event;
    }

    private function dispatchResourceEvent($eventName, ResourceControllerEvent $event)
    {
        switch ($eventName) {
            case ResourceControllerEvents::SHOW:
                $this->doDispatchResourceEvent(self::NO_PREFIX, ResourceActions::SHOW, $event);
                return;
            case ResourceControllerEvents::INDEX:
                $this->doDispatchResourceEvent(self::NO_PREFIX, ResourceActions::INDEX, $event);
                return;
            case ResourceControllerEvents::PRE_UPDATE:
                $this->doDispatchResourceEvent(self::PRE_PREFIX, ResourceActions::UPDATE, $event);
                return;
            case ResourceControllerEvents::POST_UPDATE:
                $this->doDispatchResourceEvent(self::POST_PREFIX, ResourceActions::UPDATE, $event);
                return;
            case ResourceControllerEvents::PRE_CREATE:
                $this->doDispatchResourceEvent(self::PRE_PREFIX, ResourceActions::CREATE, $event);
                return;
            case ResourceControllerEvents::POST_CREATE:
                $this->doDispatchResourceEvent(self::POST_PREFIX, ResourceActions::CREATE, $event);
                return;
            case ResourceControllerEvents::PRE_DELETE:
                $this->doDispatchResourceEvent(self::PRE_PREFIX, ResourceActions::DELETE, $event);
                return;
            case ResourceControllerEvents::POST_DELETE:
                $this->doDispatchResourceEvent(self::POST_PREFIX, ResourceActions::DELETE, $event);
                return;
        }

        throw new \RuntimeException(sprintf(
            'Do not know how to dispatch resource event "%s"', $eventName
        ));
    }

    private function doDispatchResourceEvent($prefix, $eventName, ResourceControllerEvent $event)
    {
        $requestConfiguration = $event->getRequestConfiguration();
        $eventName = $requestConfiguration->getEvent() ?: $eventName;
        $metadata = $requestConfiguration->getMetadata();

        $this->eventDispatcher->dispatch(sprintf(
            '%s.%s.%s%s',
            $metadata->getApplicationName(),
            $metadata->getName(),
            $prefix,
            $eventName
        ), $event);

        return $event;
    }
}
