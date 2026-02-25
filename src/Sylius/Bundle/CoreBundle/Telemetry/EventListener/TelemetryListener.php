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
    /** @var TelemetrySendManagerInterface */
    private $telemetrySendManager;

    /** @var string */
    private $adminApiPrefix;

    public function __construct(TelemetrySendManagerInterface $telemetrySendManager, string $adminApiPrefix)
    {
        $this->telemetrySendManager = $telemetrySendManager;
        $this->adminApiPrefix = $adminApiPrefix;
    }

    public function onAdminAccess(TerminateEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isAdminRequest($request->attributes->get('_route'), $request->getPathInfo())) {
            return;
        }

        try {
            $this->telemetrySendManager->sendIfNeeded($request);
        } catch (\Throwable $e) {
        }
    }

    private function isAdminRequest(?string $route, string $path): bool
    {
        if ($route !== null && strpos($route, 'sylius_admin_') === 0) {
            return true;
        }

        if (strpos($path, $this->adminApiPrefix) === 0) {
            return true;
        }

        return false;
    }
}
