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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/** @experimental  */
final class KernelRequestEventSubscriber implements EventSubscriberInterface
{
    /** @var bool */
    private $apiEnabled;

    /** @var string */
    private $apiRoute;

    public function __construct(bool $apiEnabled, string $apiRoute)
    {
        $this->apiEnabled = $apiEnabled;
        $this->apiRoute = $apiRoute;
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

        if ($this->apiEnabled === false && strpos($pathInfo, $this->apiRoute) !== false) {
            throw new NotFoundHttpException('Route not found');
        }
    }
}
