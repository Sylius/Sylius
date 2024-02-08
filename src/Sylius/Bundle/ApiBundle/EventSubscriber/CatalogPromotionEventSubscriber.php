<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CatalogPromotionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private CatalogPromotionAnnouncerInterface $catalogPromotionAnnouncer)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['postWrite', EventPriorities::POST_WRITE],
        ];
    }

    public function postWrite(ViewEvent $event): void
    {
        $catalogPromotion = $event->getControllerResult();

        if (!$catalogPromotion instanceof CatalogPromotionInterface) {
            return;
        }

        $method = $event->getRequest()->getMethod();
        if ($method === Request::METHOD_POST) {
            $this->catalogPromotionAnnouncer->dispatchCatalogPromotionCreatedEvent($catalogPromotion);

            return;
        }

        if (in_array($method, [Request::METHOD_PUT, Request::METHOD_PATCH], true)) {
            $this->catalogPromotionAnnouncer->dispatchCatalogPromotionUpdatedEvent($catalogPromotion);
        }
    }
}
