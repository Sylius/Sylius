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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $eventBus;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
        ];
    }

    public function postWrite(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();

        if (!$entity instanceof CatalogPromotionInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();

        if ($method === Request::METHOD_PUT || $method === Request::METHOD_PATCH) {
            $this->eventBus->dispatch(new CatalogPromotionUpdated($entity->getCode()));
        }
    }
}
