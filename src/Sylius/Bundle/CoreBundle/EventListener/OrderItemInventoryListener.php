<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderItemInventoryListener
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $item = $args->getEntity();

        if (!$this->supports($item)) {
            return;
        }

        $this->eventDispatcher->dispatch('sylius.order_item.pre_create', new GenericEvent($item));
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $eventManager = $em->getEventManager();
        $uow = $em->getUnitOfWork();

        foreach ($listeners = $eventManager->getListeners('onFlush') as $listener) {
            $eventManager->removeEventListener('onFlush', $listener);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($this->supports($entity)) {
                $this->eventDispatcher->dispatch('sylius.order_item.pre_update', new GenericEvent($entity));
                $uow->commit($entity);
            }
        }

        foreach ($listeners as $listener) {
            $eventManager->addEventListener('onFlush', $listener);
        }
    }

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    protected function supports($entity)
    {
        return $entity instanceof OrderItemInterface;
    }
}
