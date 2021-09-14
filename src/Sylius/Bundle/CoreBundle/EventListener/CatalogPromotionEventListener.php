<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventListener
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof CatalogPromotionInterface) {
            $this->eventBus->dispatch(new CatalogPromotionCreated($entity->getCode()));
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof CatalogPromotionInterface) {
            $this->eventBus->dispatch(new CatalogPromotionUpdated($entity->getCode()));
        }
    }
}
