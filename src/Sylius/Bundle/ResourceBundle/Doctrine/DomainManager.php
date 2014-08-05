<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Domain manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
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
     * @var string
     */
    private $className;

    public function __construct(
        ObjectManager $manager,
        EventDispatcherInterface $eventDispatcher,
        $bundlePrefix,
        $resourceName,
        ClassMetadata $class
    ) {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->className = $class->name;
    }

    /**
     * @return object
     */
    public function createNew()
    {
        return new $this->className();
    }

    /**
     * @return object|null
     */
    public function create()
    {
        $resource = new $this->className();
        $event = $this->dispatchEvent('pre_create', new ResourceEvent($resource));

        if ($event->isStopped()) {
            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_create', new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     *
     * @return object|null
     */
    public function update($resource)
    {
        $event = $this->dispatchEvent('pre_update', new ResourceEvent($resource));

        if ($event->isStopped()) {
            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_update', new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     *
     * @return object|null
     */
    public function delete($resource)
    {
        $event = $this->dispatchEvent('pre_delete', new ResourceEvent($resource));

        if ($event->isStopped()) {
            return null;
        }

        $this->manager->remove($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_delete', new ResourceEvent($resource));

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
        return $this->eventDispatcher->dispatch($this->getEventName($name), $event);
    }

    private function getEventName($eventName)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $eventName);
    }
}
