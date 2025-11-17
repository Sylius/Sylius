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
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use DMore\ChromeDriver\ChromeDriver;
use WebDriver\Exception\StaleElementReference;

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

    /**
     * Waits for the DOM to settle (complete loading, no pending animations, no loading indicators).
     * This is more robust than waitForPageToLoad for dynamic content updates.
     */
    public static function waitForDomSettled(Session $session, int $timeout = 2000): void
    {
        if (!self::isJavascript($session->getDriver())) {
            return;
        }

        $session->wait($timeout, <<<JS
            document.readyState === 'complete'
            && !document.querySelector('[data-live-is-loading]')
            && !document.querySelector('.loading')
            && typeof jQuery !== 'undefined' ? !jQuery.active : true
        JS);

        // Additional small wait to ensure DOM mutations have settled
        usleep(50000); // 50ms
    }

    /**
     * Performs a guarded click that handles stale elements, scrolling, and WebDriver timing issues.
     * Retries on StaleElementReference and uses JavaScript click as fallback if needed.
     */
    public static function guardedClick(Session $session, NodeElement $element, int $maxRetries = 3): void
    {
        if (!self::isJavascript($session->getDriver())) {
            $element->click();

            return;
        }

        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxRetries) {
            try {
                // Scroll element into view
                $session->executeScript(
                    'arguments[0].scrollIntoView({behavior: "instant", block: "center", inline: "center"});',
                    [$element->getXpath()],
                );

                // Wait a moment for scroll to complete
                usleep(100000); // 100ms

                // Try standard click first
                $element->click();

                return;
            } catch (StaleElementReference $e) {
                $lastException = $e;
                ++$attempt;

                if ($attempt >= $maxRetries) {
                    break;
                }

                // Wait before retry
                usleep(200000); // 200ms
            } catch (\Exception $e) {
                // Fallback to JavaScript click for other exceptions
                try {
                    $session->executeScript('arguments[0].click();', [$element->getXpath()]);

                    return;
                } catch (\Exception $jsException) {
                    $lastException = $jsException;
                    ++$attempt;

                    if ($attempt >= $maxRetries) {
                        break;
                    }

                    usleep(200000); // 200ms
                }
            }
        }

        throw new \RuntimeException(
            sprintf('Failed to click element after %d attempts. Last error: %s', $maxRetries, $lastException?->getMessage() ?? 'Unknown'),
            0,
            $lastException,
        );
    }
}
