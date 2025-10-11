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
     * Wait for Symfony UX Live Component to finish updating.
     * Strategy: Fast when stable, retry when needed.
     *
     * - If no Live Components exist: returns immediately (0ms)
     * - If busy NOW: wait up to 2s for it to finish
     * - If not busy NOW: check once (100ms) if it appears, then wait if needed
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
            // No Live Components - return immediately
            return;
        }

        // Check if busy NOW (no wait)
        $hasBusyComponents = $session->evaluateScript('!!document.querySelector("[busy]")');

        if ($hasBusyComponents) {
            // If busy components exist NOW, wait for them to finish (max 2s)
            $session->wait(2000, '!document.querySelector("[busy]")');
        } else {
            // Give busy attribute 100ms to appear (reduced from 200ms)
            $session->wait(100, 'document.querySelector("[busy]")');

            if ($session->evaluateScript('!!document.querySelector("[busy]")')) {
                // It appeared - now wait for it to finish
                $session->wait(2000, '!document.querySelector("[busy]")');
            }
        }
    }
}
