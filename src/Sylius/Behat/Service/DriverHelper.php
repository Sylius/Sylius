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

namespace Sylius\Behat\Service;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;

abstract class DriverHelper
{
    public static function isJavascript(DriverInterface $driver): bool
    {
        return $driver instanceof Selenium2Driver || $driver instanceof ChromeDriver || $driver instanceof PantherDriver;
    }

    public static function isNotJavascript(DriverInterface $driver): bool
    {
        return !$driver instanceof Selenium2Driver && !$driver instanceof ChromeDriver && !$driver instanceof PantherDriver;
    }

    public static function waitForPageToLoad(Session $session): void
    {
        if (!self::isJavascript($session->getDriver())) {
            return;
        }

        // Check if any page is loaded first (avoid triggering base_url navigation)
        $currentUrl = $session->getCurrentUrl();
        if (empty($currentUrl) || $currentUrl === 'about:blank' || $currentUrl === 'data:,') {
            // No page loaded yet - skip wait (first visit() will load a page)
            return;
        }

        // Quick check: document ready (max 1s)
        $session->wait(1000, "document.readyState === 'complete'");
    }

    /**
     * Wait for ALL Symfony UX Live Components to finish updating.
     * Strategy: Fast when stable, waits for multiple components when needed.
     *
     * - If no Live Components exist: returns immediately (0ms)
     * - Waits up to 500ms for ANY busy attribute to appear
     * - Then waits up to 3s for ALL busy attributes to disappear
     * - Handles multiple Live Components updating simultaneously
     */
    public static function waitForLiveComponentUpdate(Session $session): void
    {
        if (!self::isJavascript($session->getDriver())) {
            return;
        }

        // Fast path: Check if there are ANY Live Components on page
        $hasLiveComponents = $session->evaluateScript(
            '!!document.querySelector("[data-controller~=live]") || !!document.querySelector("[data-live-loading]")'
        );

        if (!$hasLiveComponents) {
            // No Live Components - return immediately (0ms)
            return;
        }

        // Strategy: Poll continuously for busy state changes
        // This catches multiple LCs that may start/finish at different times
        $maxWaitMs = 2000;  // Max 2s total
        $checkIntervalMs = 50;  // Check every 50ms for responsiveness
        $totalWaitedMs = 0;
        $stableCount = 0;  // Count consecutive checks with no busy
        $requiredStableChecks = 3;  // Need 3 consecutive "no busy" to be sure (150ms)

        while ($totalWaitedMs < $maxWaitMs) {
            $busyCount = $session->evaluateScript('document.querySelectorAll("[busy]").length');

            if ($busyCount === 0) {
                $stableCount++;
                if ($stableCount >= $requiredStableChecks) {
                    // Stable for 150ms with no busy - all LCs done
                    return;
                }
            } else {
                // Reset stability counter if busy appears
                $stableCount = 0;
            }

            usleep($checkIntervalMs * 1000);
            $totalWaitedMs += $checkIntervalMs;
        }
    }
}
