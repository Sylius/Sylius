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
use Sylius\Component\Core\Event\ProductVariantCreated;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductVariantEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private MessageBusInterface $eventBus)
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
        $variant = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$variant instanceof ProductVariantInterface) {
            return;
        }

        if ($method === Request::METHOD_POST) {
            $this->eventBus->dispatch(new ProductVariantCreated($variant->getCode()));

            return;
        }

        if ($method === Request::METHOD_PUT) {
            $this->eventBus->dispatch(new ProductVariantUpdated($variant->getCode()));
        }
    }
}
