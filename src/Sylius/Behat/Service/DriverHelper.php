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

        // Optionally install MutationObserver for debugging Live Components
        // Uncomment when debugging Symfony UX Live Component issues
        // $session->evaluateScript(<<<'JS'
        //     if (!window.__behat_observer_installed) {
        //         window.__behat_observer_installed = true;
        //         new MutationObserver((mutations) => {
        //             mutations.forEach(m => {
        //                 if (m.attributeName === 'busy' || m.attributeName?.includes('live')) {
        //                     console.log('🔔 BEHAT DEBUG - MUTATION:', m.attributeName, '→', m.target.getAttribute(m.attributeName), m.target);
        //                 }
        //             });
        //         }).observe(document.body, { attributes: true, subtree: true });
        //         console.log('✅ Behat MutationObserver installed');
        //     }
        // JS);

        // Wait for document ready first
        $session->wait(5000, "document.readyState === 'complete'");

        // CRITICAL FIX: Wait for Live Components to finish updating
        // Problem: querySelector('[busy]') returns null BOTH when:
        //   1. Component is not loading (good)
        //   2. Component hasn't started loading yet (race condition!)
        //
        // Solution: Use a smarter check that waits for stability
        $hasBusyComponents = $session->evaluateScript('!!document.querySelector("[busy]")');

        if ($hasBusyComponents) {
            // If busy components exist NOW, wait for them to finish (max 10s)
            $session->wait(10000, '!document.querySelector("[busy]")');
        } else {
            // No busy components right now - wait a bit to see if any appear (max 500ms)
            // This catches components that start loading slightly after action
            $session->wait(500, 'document.querySelector("[busy]")');

            // If busy appeared during that wait, now wait for it to finish
            if ($session->evaluateScript('!!document.querySelector("[busy]")')) {
                $session->wait(10000, '!document.querySelector("[busy]")');
            }
        }

        // Also check for legacy data-live-loading
        $session->wait(1000, '!document.querySelector("[data-live-loading=true]")');
    }
}
