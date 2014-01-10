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

/**
 * Domain manager.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class DomainManager
{
    private $manager;
    private $eventDispatcher;
    private $flashHelper;
    private $config;

    public function __construct(ObjectManager $manager, EventDispatcherInterface $eventDispatcher, FlashHelper $flashHelper, Configuration $config)
    {
        $this->manager = $manager;
        $this->eventDispatcher = $eventDispatcher;
        $this->flashHelper = $flashHelper;
        $this->config = $config;
    }

    public function create($resource)
    {
        $event = $this->dispatchEvent('pre_create', new ResourceEvent($resource));

        if ($event->isStopped()) {
            $this->flashHelper->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();
        $this->flashHelper->setFlash('success', 'create');

        $this->dispatchEvent('post_create', new ResourceEvent($resource));

        return $resource;
    }

    public function update($resource)
    {
        $event = $this->dispatchEvent('pre_update', new ResourceEvent($resource));

        if ($event->isStopped()) {
            $this->flashHelper->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            return null;
        }

        $this->manager->persist($resource);
        $this->manager->flush();
        $this->flashHelper->setFlash('success', 'update');

        $this->dispatchEvent('post_update', new ResourceEvent($resource));

        return $resource;
    }

    public function delete($resource)
    {
        $event = $this->dispatchEvent('pre_delete', new ResourceEvent($resource));

        if ($event->isStopped()) {
            $this->flashHelper->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParameters());

            return null;
        }

        $this->manager->remove($resource);
        $this->manager->flush();
        $this->flashHelper->setFlash('success', 'delete');

        $this->dispatchEvent('post_delete', new ResourceEvent($resource));

        return $resource;
    }


    public function dispatchEvent($name, Event $event)
    {
        $name = $this->config->getEventName($name);

        return $this->eventDispatcher->dispatch($name, $event);
    }
}
