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

namespace Sylius\Bundle\CoreBundle\Telemetry\EventListener;

use Sylius\Component\Core\Telemetry\TelemetrySendManagerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

/** @internal */
class TelemetryListener
{
    public function __construct(
        private TelemetrySendManagerInterface $telemetrySendManager,
        private string $adminApiPrefix,
    ) {
    }

    public function onAdminAccess(TerminateEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isAdminRequest($request->attributes->get('_route'), $request->getPathInfo())) {
            return;
        }

        try {
            $this->telemetrySendManager->sendIfNeeded();
        } catch (\Throwable) {
        }
    }

    private function isAdminRequest(?string $route, string $path): bool
    {
        if ($route !== null && str_starts_with($route, 'sylius_admin_')) {
            return true;
        }

        if (str_starts_with($path, $this->adminApiPrefix)) {
            return true;
        }

        return false;
    }
}
