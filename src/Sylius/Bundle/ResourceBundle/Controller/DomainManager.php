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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Exception\DomainException;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Domain manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class DomainManager
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(
        ObjectManager $manager,
        EventDispatcherInterface $eventDispatcher,
        Configuration $config
    ) {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->config = $config;
    }

    /**
     * @param object $resource
     *
     * @return object
     *
     * @throws DomainException
     */
    public function create($resource)
    {
        $eventName = $this->config->getEvent('create');
        $event = $this->dispatchEvent(sprintf('pre_%s', $eventName), new ResourceEvent($resource));

        if ($event->isStopped()) {
            throw new DomainException($event->getMessageType(), $event->getMessage(), $event->getErrorCode(), $event->getMessageParameters());
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent(sprintf('post_%s', $eventName), new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     *
     * @return object
     *
     * @throws DomainException
     */
    public function update($resource)
    {
        $eventName = $this->config->getEvent('update');
        $event = $this->dispatchEvent(sprintf('pre_%s', $eventName), new ResourceEvent($resource));

        if ($event->isStopped()) {
            throw new DomainException($event->getMessageType(), $event->getMessage(), $event->getErrorCode(), $event->getMessageParameters());
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent(sprintf('post_%s', $eventName), new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     * @param int    $movement
     *
     * @return object|ResourceEvent|null
     */
    public function move($resource, $movement)
    {
        $position = $this->config->getSortablePosition();

        $accessor = PropertyAccess::createPropertyAccessor();

        $accessor->setValue(
            $resource,
            $position,
            $accessor->getValue($resource, $position) + $movement
        );

        return $this->update($resource);
    }

    /**
     * @param object $resource
     *
     * @return object
     *
     * @throws DomainException
     */
    public function delete($resource)
    {
        $eventName = $this->config->getEvent('delete');
        $event = $this->dispatchEvent(sprintf('pre_%s', $eventName), new ResourceEvent($resource));

        if ($event->isStopped()) {
            throw new DomainException($event->getMessageType(), $event->getMessage(), $event->getErrorCode(), $event->getMessageParameters());
        }

        $this->manager->remove($resource);
        $this->manager->flush();

        $this->dispatchEvent(sprintf('post_%s', $eventName), new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param string $name
     * @param Event  $event
     *
     * @return ResourceEvent
     */
    public function dispatchEvent($name, Event $event)
    {
        $name = $this->config->getEventName($name);

        return $this->eventDispatcher->dispatch($name, $event);
    }
}
