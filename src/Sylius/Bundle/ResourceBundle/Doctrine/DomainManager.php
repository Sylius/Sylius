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
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Domain manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DomainManager implements DomainManagerInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $resourceName;

    /**
     * @var string
     */
    protected $bundlePrefix;

    /**
     * @var string
     */
    protected $className;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, $bundlePrefix, $resourceName, ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->bundlePrefix = $bundlePrefix;
        $this->resourceName = $resourceName;
        $this->className = $class->name;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new $this->className();
    }

    /**
     * {@inheritdoc}
     */
    public function create($resource = null, $eventName = 'create')
    {
        if (null === $resource) {
            $resource = $this->createNew();
        }

        $event = $this->dispatchEvent('pre_'.$eventName, new ResourceEvent($resource));
        if ($event->isStopped()) {
            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_'.$eventName, new ResourceEvent($resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function update($resource, $eventName = 'update')
    {
        $event = $this->dispatchEvent('pre_'.$eventName, new ResourceEvent($resource));
        if ($event->isStopped()) {
            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_'.$eventName, new ResourceEvent($resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($resource, $eventName = 'delete')
    {
        $event = $this->dispatchEvent('pre_'.$eventName, new ResourceEvent($resource));

        if ($event->isStopped()) {
            return null;
        }

        $this->manager->remove($resource);
        $this->manager->flush();

        $this->dispatchEvent('post_'.$eventName, new ResourceEvent($resource));

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function bulk($action = 'create', array $resources)
    {
        if (!in_array($action, array('create', 'update', 'delete'))) {
            throw new \InvalidArgumentException();
        }

        foreach ($resources as $resource) {
            $event = $this->dispatchEvent('pre_'.$action, new ResourceEvent($resource));
            if ($event->isStopped()) {
                continue;
            }

            if ('delete' === $action) {
                $this->manager->remove($resource);
            } else {
                $this->manager->persist($resource);
            }

            $this->dispatchEvent('post_'.$action, new ResourceEvent($resource));
        }

        $this->manager->flush();

        return $resources;
    }

    /**
     * @param string $name
     * @param Event  $event
     *
     * @return ResourceEvent
     */
    protected function dispatchEvent($name, Event $event)
    {
        return $this->eventDispatcher->dispatch($this->getEventName($name), $event);
    }

    private function getEventName($eventName)
    {
        return sprintf('%s.%s.%s', $this->bundlePrefix, $this->resourceName, $eventName);
    }
}
