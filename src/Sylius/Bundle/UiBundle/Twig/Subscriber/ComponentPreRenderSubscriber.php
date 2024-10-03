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

namespace Sylius\Bundle\UiBundle\Twig\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final class ComponentPreRenderSubscriber implements EventSubscriberInterface
{
    public function onPreRender(PreRenderEvent $event): void
    {
        $template = $event->getVariables()['template'] ?? null;

        if (null === $template) {
            return;
        }

        $event->setTemplate($template);
    }

    public static function getSubscribedEvents(): array
    {
        return [PreRenderEvent::class => 'onPreRender'];
    }
}
