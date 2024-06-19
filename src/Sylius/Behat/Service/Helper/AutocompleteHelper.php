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

    public function search(DriverInterface $driver, string $selector, string $searchString): mixed
    {
        $selector = $this->normalizeSelector($selector);
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
        $foundItems = array_flip($this->search($driver, $selector, $name));

        $value = $this->getValueByPhrase($foundItems, $name);

        $this->addItemByValue($driver, $selector, $value);
    }

    public function removeByName(DriverInterface $driver, string $selector, string $name): void
    {
        $selector = $this->normalizeSelector($selector);
        $selectedItems = array_flip($this->getSelectedItems($driver, $selector));

        $value = $this->getValueByPhrase($selectedItems, $name);

        $this->removeItemByValue($driver, $selector, $value);
    }

    public function selectByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $selector = $this->normalizeSelector($selector);
        $foundItems = $this->search($driver, $selector, $value);

        if (!array_key_exists($value, $foundItems)) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete', $value));
        }

        $this->addItemByValue($driver, $selector, $value);
    }

    public function removeByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $selector = $this->normalizeSelector($selector);
        $selectedItems = $this->getSelectedItems($driver, $selector);

        if (!array_key_exists($value, $selectedItems)) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete selected items', $value));
        }

        $this->removeItemByValue($driver, $selector, $value);
    }

    private function addItemByValue(DriverInterface $driver, string $selector, int|string $value): void
    {
        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                element.tomselect.addItem('{$value}');
                element.tomselect.refreshOptions();
            })();
        SCRIPT);
    }

    private function removeItemByValue(DriverInterface $driver, string $selector, int|string $value): void
    {
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

    private function normalizeSelector(string $selector): string
    {
        return str_replace('"', '\'', $selector);
    }
}
