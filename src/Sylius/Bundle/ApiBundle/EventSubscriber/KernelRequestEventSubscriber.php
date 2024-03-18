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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelRequestEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private bool $apiEnabled,
        private string $apiRoute,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['validateApi', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function validateApi(RequestEvent $event): void
    {
        $pathInfo = $event->getRequest()->getPathInfo();

        if ($this->apiEnabled === false && str_contains($pathInfo, $this->apiRoute)) {
            throw new NotFoundHttpException('Route not found');
        }
    }
}
