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
use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
     * @var FlashHelper
     */
    private $flashHelper;

    /**
     * @var Configuration
     */
    private $config;

    public function __construct(
        ObjectManager $manager,
        EventDispatcherInterface $eventDispatcher,
        FlashHelper $flashHelper,
        Configuration $config
    ) {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->flashHelper = $flashHelper;
        $this->config = $config;
    }

    /**
     * @param object $resource
     * @param bool   $setFlash
     *
     * @return object|null
     */
    public function create($resource, $setFlash = true)
    {
        /** @var ResourceEvent $event */
        $event = $this->dispatchEvent('pre_create', new ResourceEvent($resource));

        if ($event->isStopped()) {
            if ($setFlash) {
                $this->flashHelper->setFlash(
                    $event->getMessageType(),
                    $event->getMessage(),
                    $event->getMessageParameters()
                );
            }

            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        if ($setFlash) {
            $this->flashHelper->setFlash('success', 'create');
        }

        $this->dispatchEvent('post_create', new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     * @param string $flash
     * @param bool   $setFlash
     *
     * @return object|null
     */
    public function update($resource, $flash = 'update', $setFlash = true)
    {
        /** @var ResourceEvent $event */
        $event = $this->dispatchEvent('pre_update', new ResourceEvent($resource));

        if ($event->isStopped()) {
            if ($setFlash) {
                $this->flashHelper->setFlash(
                    $event->getMessageType(),
                    $event->getMessage(),
                    $event->getMessageParameters()
                );
            }

            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();

        if ($setFlash) {
            $this->flashHelper->setFlash('success', $flash);
        }

        $this->dispatchEvent('post_update', new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param object $resource
     * @param int    $movement
     * @param bool   $setFlash
     *
     * @return null|object
     */
    public function move($resource, $movement, $setFlash = true)
    {
        $position = $this->config->getSortablePosition();

        $accessor = PropertyAccess::createPropertyAccessor();

        $accessor->setValue(
            $resource,
            $position,
            $accessor->getValue($resource, $position) + $movement
        );

        return $this->update($resource, 'move', $setFlash);
    }

    /**
     * @param object $resource
     * @param bool   $setFlash
     *
     * @return object|null
     */
    public function delete($resource, $setFlash = true)
    {
        /** @var ResourceEvent $event */
        $event = $this->dispatchEvent('pre_delete', new ResourceEvent($resource));

        if ($event->isStopped()) {
            $this->flashHelper->setFlash(
                $event->getMessageType(),
                $event->getMessage(),
                $event->getMessageParameters()
            );

            return null;
        }

        $this->manager->remove($resource);
        $this->manager->flush();

        if ($setFlash) {
            $this->flashHelper->setFlash('success', 'delete');
        }

        $this->dispatchEvent('post_delete', new ResourceEvent($resource));

        return $resource;
    }

    /**
     * @param string $name
     * @param Event  $event
     *
     * @return Event
     */
    public function dispatchEvent($name, Event $event)
    {
        $name = $this->config->getEventName($name);

        return $this->eventDispatcher->dispatch($name, $event);
    }
}
