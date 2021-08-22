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

namespace Sylius\Bundle\PromotionBundle\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Component\Promotion\Event\CatalogPromotionCreated;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['dispatchCatalogPromotionCreated', EventPriorities::POST_WRITE],
        ];
    }

    public function dispatchCatalogPromotionCreated(ViewEvent $event): void
    {
        $catalogPromotion = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$catalogPromotion instanceof CatalogPromotionInterface || Request::METHOD_POST !== $method) {
            return;
        }

        $this->messageBus->dispatch(new CatalogPromotionCreated($catalogPromotion->getCode()));
    }
}
