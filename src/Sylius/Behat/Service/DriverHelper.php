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
        if (self::isJavascript($session->getDriver())) {
            $session->wait(1000, "document.readyState === 'complete' && !document.querySelector('[data-live-is-loading]')");
        }
    }

    public static function waitForElement(Session $session, string $selector, int $timeout = 5000): void
    {
        if (self::isJavascript($session->getDriver())) {
            $session->wait($timeout, sprintf(
                'document.querySelector(%s) !== null',
                json_encode($selector),
            ));
        }
    }

    public static function waitForAsynchronousActionsToFinish(Session $session): void
    {
        $session->wait(1000, "!document.querySelector('[data-live-is-loading]')");
    }

    public static function waitForFormToStopLoading(Session $session, int $timeout = 1000): void
    {
        if (self::isJavascript($session->getDriver())) {
            $session->wait($timeout, "document.readyState === 'complete' && !document.querySelector('[data-live-is-loading]')");
        }
    }

    public static function waitForLiveComponentUpdate(Session $session, int $timeout = 5000): void
    {
        if (self::isNotJavascript($session->getDriver())) {
            return;
        }

        // Give the Live Component a brief, bounded chance to START the request.
        // The "busy" attribute is set by ux-live-component on the Live Component root element
        // (the one carrying the "live" Stimulus controller), not on the form. We scope the "busy"
        // probe to that root so that an unrelated "busy" attribute appearing elsewhere in the DOM
        // cannot stall the wait; the "data-live-is-loading" marker is Live Component specific already.
        $session->wait(1000, "document.querySelectorAll('[data-controller~=\"live\"][busy], [data-live-is-loading]').length > 0");

        // Wait until ALL Live Component requests have FINISHED.
        $session->wait(
            $timeout,
            "document.readyState === 'complete' && " .
            "document.querySelectorAll('[data-controller~=\"live\"][busy], [data-live-is-loading]').length === 0",
        );
    }
}
