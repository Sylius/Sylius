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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\EventListener\LocaleAwareListener as DecoratedLocaleListener;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleAwareListener implements EventSubscriberInterface
{
    public function __construct(private DecoratedLocaleListener $decoratedListener)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->decoratedListener->onKernelRequest($event);
    }

    public function onKernelFinishRequest(FinishRequestEvent $event): void
    {
        $this->decoratedListener->onKernelFinishRequest($event);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered after the Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 4]],
            KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', -15]],
        ];
    }
}
