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

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Driver\DriverInterface;

final class AutocompleteHelper implements AutocompleteHelperInterface
{
    public function getSelectedItems(DriverInterface $driver, string $selector): array
    {
        $selector = $this->normalizeSelector($selector);
        $result = $driver->evaluateScript(<<<SCRIPT
            (function () {
                let select = document.evaluate("//SELECT[{$selector}]", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                let selectedOptions = [];

                [...select.options].forEach((option) => selectedOptions[option.value] = option.textContent);

                return selectedOptions;
            })();
        SCRIPT);

        return is_array($result) ? $result : [];
    }

    public function search(DriverInterface $driver, string $selector, string $searchString, bool $wait = true): mixed
    {
        $selector = $this->normalizeSelector($selector);

        // Wait for element and tomselect to be ready (max 2s) only if requested
        if ($wait) {
            $this->waitForTomselect($driver, $selector);
        }

        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                element.tomselect.load('$searchString');
                element.tomselect.open();
            })();
        SCRIPT);

        $driver->wait(
            2000,
            <<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                return element.tomselect.loading === 0;
            })();
            SCRIPT,
        );

        return $driver->evaluateScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                let searchResults = [];

                element.parentElement.querySelectorAll('[data-selectable]').forEach((node) => searchResults[node.dataset.value] = node.textContent);

                return searchResults;
            })();
        SCRIPT);
    }

    public function selectByName(DriverInterface $driver, string $selector, string $name): void
    {
        $selector = $this->normalizeSelector($selector);

        // Wait once for tomselect to be ready before searching
        $this->waitForTomselect($driver, $selector);

        $foundItems = array_flip($this->search($driver, $selector, $name, false));

        $value = $this->getValueByPhrase($foundItems, $name);

        $this->addItemByValue($driver, $selector, $value, false);
    }

    public function removeByName(DriverInterface $driver, string $selector, string $name): void
    {
        $selector = $this->normalizeSelector($selector);

        // Wait once for tomselect to be ready
        $this->waitForTomselect($driver, $selector);

        $selectedItems = array_flip($this->getSelectedItems($driver, $selector));

        $value = $this->getValueByPhrase($selectedItems, $name);

        $this->removeItemByValue($driver, $selector, $value, false);
    }

    public function selectByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $selector = $this->normalizeSelector($selector);

        // Wait once for tomselect to be ready
        $this->waitForTomselect($driver, $selector);

        // Since we already know the value, add it directly without searching
        // This saves 2s of waiting for loading === 0
        $this->addItemByValue($driver, $selector, $value, false);
    }

    public function removeByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $selector = $this->normalizeSelector($selector);

        // Wait once for tomselect to be ready
        $this->waitForTomselect($driver, $selector);

        $selectedItems = $this->getSelectedItems($driver, $selector);

        if (!array_key_exists($value, $selectedItems)) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete selected items', $value));
        }

        $this->removeItemByValue($driver, $selector, $value, false);
    }

    public function clear(DriverInterface $driver, string $selector): void
    {
        $selector = $this->normalizeSelector($selector);

        // Wait for element and tomselect to be ready
        $this->waitForTomselect($driver, $selector);

        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                element.tomselect.clear();
                element.tomselect.refreshOptions();
            })();
        SCRIPT);
    }

    private function addItemByValue(DriverInterface $driver, string $selector, int|string $value, bool $wait = true): void
    {
        // Wait for element and tomselect to be ready (max 2s) only if requested
        if ($wait) {
            $this->waitForTomselect($driver, $selector);
        }

        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                element.tomselect.addItem('{$value}');
                element.tomselect.refreshOptions();
            })();
        SCRIPT);
    }

    private function removeItemByValue(DriverInterface $driver, string $selector, int|string $value, bool $wait = true): void
    {
        // Wait for element and tomselect to be ready (max 2s) only if requested
        if ($wait) {
            $this->waitForTomselect($driver, $selector);
        }

        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                element.tomselect.removeItem('{$value}');
                element.tomselect.refreshOptions();
            })();
        SCRIPT);
    }

    private function getValueByPhrase(array $foundItems, string $phrase): int|string
    {
        foreach ($foundItems as $foundName => $foundValue) {
            if (str_contains($foundName, $phrase)) {
                return $foundValue;
            }
        }

        throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete', $phrase));
    }

    private function waitForTomselect(DriverInterface $driver, string $selector): void
    {
        $start = microtime(true);

        error_log(sprintf('[AUTOCOMPLETE-WAIT] Waiting for tomselect on selector: %s', substr($selector, 0, 100)));

        // Wait for element and tomselect to be ready (max 5s for LiveComponent + init)
        $result = $driver->wait(5000, <<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                return element && element.tomselect;
            })();
        SCRIPT);

        $duration = round((microtime(true) - $start) * 1000, 2);

        if ($duration > 100) {
            error_log(sprintf('[AUTOCOMPLETE-WAIT] tomselect ready: %.2f ms (result: %s)', $duration, $result ? 'true' : 'false'));
        }

        if (!$result) {
            error_log('[AUTOCOMPLETE-WAIT] TIMEOUT: tomselect not ready after 5s');
            error_log(sprintf('[AUTOCOMPLETE-WAIT] Selector: %s', $selector));

            // Debug: comprehensive diagnostics
            $diagnostics = $driver->evaluateScript(<<<SCRIPT
                (function () {
                    let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;

                    if (!element) {
                        return { elementExists: false };
                    }

                    let parent = element.closest('[data-controller]');

                    return {
                        elementExists: true,
                        hasTomselect: !!element.tomselect,
                        elementType: element.tagName,
                        hasDataController: !!element.getAttribute('data-controller'),
                        dataController: element.getAttribute('data-controller'),
                        parentController: parent ? parent.getAttribute('data-controller') : null,
                        hasAutocompleteController: element.getAttribute('data-controller')?.includes('autocomplete'),
                        stimulusConnected: element.hasAttribute('data-symfony--ux-autocomplete--autocomplete-connected'),
                    };
                })();
            SCRIPT);

            error_log(sprintf('[AUTOCOMPLETE-WAIT] Diagnostics: %s', json_encode($diagnostics)));

            // Check LiveComponent debug logs
            $liveComponentDebug = $driver->evaluateScript(<<<SCRIPT
                (function () {
                    if (!window.__liveComponentDebug) {
                        return { available: false };
                    }

                    return {
                        available: true,
                        totalLogs: window.__liveComponentDebug.logs.length,
                        events: window.__liveComponentDebug.events,
                        lastLogs: window.__liveComponentDebug.logs.slice(-5)
                    };
                })();
            SCRIPT);

            error_log(sprintf('[AUTOCOMPLETE-WAIT] LiveComponent Debug Info: %s', json_encode($liveComponentDebug)));
        }
    }

    private function normalizeSelector(string $selector): string
    {
        return str_replace('"', '\'', $selector);
    }
}
