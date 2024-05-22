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

namespace Sylius\Bundle\AdminBundle\EventListener;

use Symfony\UX\TwigComponent\AnonymousComponent;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;

final class TwigComponentListener
{
    public function onPreRender(PreRenderEvent $event): void
    {
        if (!str_starts_with($event->getMetadata()->getName(), 'sylius_admin:')) {
            return;
        }

        if (!$event->getComponent() instanceof AnonymousComponent) {
            return;
        }

        if (!array_key_exists('hookableMetadata', $event->getVariables())) {
            return;
        }

        $variables = $event->getVariables();
        $variables['hookable_metadata'] = $variables['hookableMetadata'];
        unset($variables['hookableMetadata']);

        $event->setVariables($variables);
    }
}
