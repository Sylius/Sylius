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

namespace Sylius\Behat\Behaviour;

use Behat\Mink\Driver\PantherDriver;
use Behat\Mink\Driver\Selenium2Driver;
use DMore\ChromeDriver\ChromeDriver;
use Sylius\Behat\Context\Ui\WaitDebugContext;
use Sylius\Behat\Element\NodeElement;
use Sylius\Behat\Element\NodeElement as SyliusNodeElement;

trait WaitsForElements
{
    protected function getElement(string $name, array $parameters = []): SyliusNodeElement
    {
        $this->waitForPageToLoad();

        $element = parent::getElement($name, $parameters);

        // Wrap in SyliusNodeElement to add automatic waiting after click
        $wrappedElement = new SyliusNodeElement($element->getXpath(), $this->getSession());

        // Don't wait for interactability here - let it be lazy
        // The wrapped NodeElement will wait when methods like click(), setValue(), etc. are called

        return $wrappedElement;
    }

    /**
     * Waits for the page to finish loading.
     *
     * Checks that:
     * 1. Document is ready (document.readyState === 'complete')
     * 2. No Symfony UX Live Components are currently loading ([data-live-is-loading] is not present)
     */
    protected function waitForPageToLoad(): void
    {
        if (!$this->isJavascript()) {
            return;
        }

        $start = microtime(true);
        $result = $this->getSession()->wait(5000, "document.readyState === 'complete' && !document.querySelector('[data-live-is-loading]')");
        $duration = round((microtime(true) - $start) * 1000, 2);

        if ($duration > 0) {
            WaitDebugContext::recordWait('waitForPageToLoad', $duration);
        }

        // If timeout, log current state for debugging
        if (!$result) {
            $status = $this->getSession()->evaluateScript(
                "(function() {
                    return {
                        readyState: document.readyState,
                        loading: document.querySelectorAll('[data-live-is-loading]').length,
                        url: window.location.href
                    };
                })()",
            );

            error_log(sprintf(
                '[PAGE-LOAD-TIMEOUT] After 5s: readyState=%s, loading=%d, url=%s',
                $status['readyState'],
                $status['loading'],
                $status['url'],
            ));
        }
    }

    /**
     * Waits for ALL LiveComponents to complete their operations.
     *
     * Returns immediately if condition is already met.
     * Max 5 seconds for: DOM ready, no loading indicators, no busy elements.
     */
    protected function waitForLiveComponentToFinish(): void
    {
        if (!$this->isJavascript()) {
            return;
        }

        $start = microtime(true);
        $result = $this->getSession()->wait(
            5000,
            "document.readyState === 'complete' && " .
            "document.querySelectorAll('[data-live-is-loading]').length === 0 && " .
            "document.querySelectorAll('[busy]').length === 0",
        );
        $duration = round((microtime(true) - $start) * 1000, 2);

        // Log if we actually waited
        if ($duration > 0) {
            WaitDebugContext::recordWait('waitForLiveComponentToFinish', $duration);
        }

        // Double-check: verify LiveComponent really finished
        if ($result) {
            $status = $this->getSession()->evaluateScript(
                "(function() {
                    return {
                        loading: document.querySelectorAll('[data-live-is-loading]').length,
                        busy: document.querySelectorAll('[busy]').length,
                        readyState: document.readyState
                    };
                })()",
            );

            if ($status['loading'] > 0 || $status['busy'] > 0) {
                error_log(sprintf(
                    '[LIVECOMPONENT-RACE] Wait returned true but found: loading=%d, busy=%d - possible race condition!',
                    $status['loading'],
                    $status['busy'],
                ));
            }
        } else {
            // Timeout - log what prevented completion
            $status = $this->getSession()->evaluateScript(
                "(function() {
                    return {
                        loading: document.querySelectorAll('[data-live-is-loading]').length,
                        busy: document.querySelectorAll('[busy]').length,
                        readyState: document.readyState
                    };
                })()",
            );

            error_log(sprintf(
                '[LIVECOMPONENT-TIMEOUT] After 5s: loading=%d, busy=%d, readyState=%s',
                $status['loading'],
                $status['busy'],
                $status['readyState'],
            ));
        }
    }

    /**
     * Waits for element to be visible and ready for interaction (with polling mechanism).
     *
     * How wait() works:
     * - Executes JS code repeatedly every ~100-200ms (polling)
     * - If result is truthy (true, object, etc.) → stops waiting immediately
     * - If result is falsy (false, null, undefined) → waits and tries again
     * - Repeats until timeout (5000ms) or success
     *
     * Element is considered ready when it's:
     * 1. Visible: offsetParent !== null AND display !== "none"
     * 2. Interactable: not disabled, not readonly, not busy, not loading
     */
    protected function waitForElementToBeInteractable(NodeElement $element, int $timeout = 5000): void
    {
        if (!$this->isJavascript()) {
            return;
        }

        $xpath = str_replace('"', '\"', $element->getXpath());
        $start = microtime(true);

        $result = $this->getSession()->wait($timeout, sprintf(
            '(function() {
                // Step 1: Find element in DOM by XPath
                var el = document.evaluate(
                    "%s",
                    document,
                    null,
                    XPathResult.FIRST_ORDERED_NODE_TYPE,
                    null
                ).singleNodeValue;

                // Step 2: Element not found → return false (wait will poll again)
                if (!el) {
                    return false;
                }

                // Step 3: Check if element is visible
                var isVisible = el.offsetParent !== null && getComputedStyle(el).display !== "none";

                if (!isVisible) {
                    return false;
                }

                // Step 4: Check if element is interactable (not disabled, not busy, etc.)
                var isInteractable =
                    !el.disabled &&
                    !el.readOnly &&
                    !el.hasAttribute("aria-busy") &&
                    !el.hasAttribute("busy") &&
                    !el.hasAttribute("data-live-is-loading");

                // Step 5: Return true only if visible AND interactable
                return isInteractable;
            })()',
            $xpath,
        ));

        // Debug: If timeout occurred, log why element wasn't ready
        if (!$result) {
            $blockers = $this->getSession()->evaluateScript(sprintf(
                '(function() {
                    var el = document.evaluate("%s", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                    if (!el) return "NOT_FOUND";
                    var reasons = [];
                    if (el.offsetParent === null) reasons.push("hidden:offsetParent");
                    if (getComputedStyle(el).display === "none") reasons.push("hidden:display");
                    if (el.disabled) reasons.push("disabled");
                    if (el.readOnly) reasons.push("readOnly");
                    if (el.hasAttribute("aria-busy")) reasons.push("aria-busy=" + el.getAttribute("aria-busy"));
                    if (el.hasAttribute("busy")) reasons.push("busy");
                    if (el.hasAttribute("data-live-is-loading")) reasons.push("data-live-is-loading");
                    return reasons.join(", ") || "UNKNOWN";
                })()',
                str_replace('"', '\"', $element->getXpath()),
            ));

            error_log(sprintf('[WAIT-DEBUG] Element not interactable after timeout: %s', $blockers));
        }

        $duration = round((microtime(true) - $start) * 1000, 2);

        if ($duration > 0) {
            // Extract readable element identifier from XPath for logging
            $elementId = preg_match('/\[@[^=]+=(["\'])([^"\']+)\1/', $xpath, $matches) ? $matches[2] : 'unknown';
            WaitDebugContext::recordWait('waitForElementToBeInteractable', $duration, $elementId);
        }
    }

    protected function isJavascript(): bool
    {
        $driver = $this->getDriver();

        return $driver instanceof Selenium2Driver || $driver instanceof ChromeDriver || $driver instanceof PantherDriver;
    }
}
