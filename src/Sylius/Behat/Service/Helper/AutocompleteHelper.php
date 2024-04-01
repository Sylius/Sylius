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
        $result = $driver->evaluateScript(<<<SCRIPT
            element.tomselect.getValue();
        SCRIPT);

        if ('' === $result || null === $result) {
            return [];
        }

        return explode(',', $result);
    }

    public function search(DriverInterface $driver, string $selector, string $searchString): mixed
    {
        $selector = str_replace('"', "'", $selector);
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

    public function select(DriverInterface $driver, string $selector, string $value): void
    {
        $selector = str_replace('"', "'", $selector);
        $foundItems = $this->search($driver, $selector, $value);

        if (!array_key_exists($value, $foundItems)) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete', $value));
        }

        $driver->executeScript(<<<SCRIPT
            (function () {
                let element = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;

                element.tomselect.addItem('{$value}');
                element.tomselect.refreshOptions();
            })();
        SCRIPT);
    }
}
