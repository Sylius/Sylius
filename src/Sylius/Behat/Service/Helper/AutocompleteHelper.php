<?php

/*
 * This file is part of the Sylius package.
 * (c) Sylius Sp. z o.o.
 */

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

use Behat\Mink\Driver\DriverInterface;

final class AutocompleteHelper implements AutocompleteHelperInterface
{
    /**
     * Zwraca aktualnie wybrane pozycje w komponencie TomSelect jako mapę: [value => label].
     */
    public function getSelectedItems(DriverInterface $driver, string $selector): array
    {
        $selector = $this->escapeForJsDoubleQuoted($selector);

        $result = $driver->evaluateScript(<<<JS
            (function () {
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                if (!el) return {};
                var ts = el.tomselect;
                var out = {};
                if (ts) {
                    (ts.items || []).forEach(function (val) {
                        var opt = ts.options[val];
                        out[val] = (opt && (opt.text || opt.title || opt.label)) ? String(opt.text || opt.title || opt.label).trim() : String(val);
                    });
                } else if (el.options) {
                    for (var i = 0; i < el.options.length; i++) {
                        var opt = el.options[i];
                        if (opt.selected) out[opt.value] = (opt.textContent || '').trim();
                    }
                }
                return out;
            })();
        JS);

        return is_array($result) ? $result : [];
    }

    /**
     * Wyszukuje w dropdownie TS — zwraca mapę wyników: [value => label].
     */
    public function search(DriverInterface $driver, string $selector, string $searchString): array
    {
        $selector = $this->escapeForJsDoubleQuoted($selector);
        $search   = $this->toJsString($searchString);

        // Poczekaj aż TomSelect się zainicjalizuje
        $driver->wait(3000, <<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                return !!(el && el.tomselect);
            })();
        JS);

        // Otwórz, ustaw frazę — bez klikania ukrytych elementów
        $driver->executeScript(<<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                var ts = el.tomselect;
                el.closest('.ts-wrapper')?.scrollIntoView({block:'center', inline:'center'});
                ts.open();
                ts.focus();
                ts.setTextboxValue({$search});
                if (typeof ts.onSearchChange === 'function') { ts.onSearchChange({$search}); }
            })();
        JS);

        // Czekaj aż skończy ładować i dropdown jest otwarty
        $driver->wait(4000, <<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                var ts = el && el.tomselect;
                if (!ts) return false;
                var open = !!(ts.isOpen || (ts.dropdown && ts.dropdown.classList.contains('ts-dropdown') && !ts.dropdown.classList.contains('hidden')));
                var notLoading = (ts.loading === 0);
                return open && notLoading;
            })();
        JS);

        // Zbierz wyniki z dropdownu
        $result = $driver->evaluateScript(<<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                var ts = el.tomselect;
                var out = {};
                var c = (ts && ts.dropdown_content) ? ts.dropdown_content : el.parentElement;
                var nodes = c ? c.querySelectorAll('[data-selectable]') : [];
                nodes.forEach(function(n){
                    var val = n.getAttribute('data-value');
                    var label = (n.textContent || '').trim();
                    if (val) out[val] = label;
                });
                return out;
            })();
        JS);

        return is_array($result) ? $result : [];
    }

    /**
     * Wybierz pozycję po nazwie (najpierw exact, potem contains).
     */
    public function selectByName(DriverInterface $driver, string $selector, string $name): void
    {
        $found = $this->search($driver, $selector, $name);
        if ($found === []) {
            throw new \InvalidArgumentException(sprintf('No results returned for "%s".', $name));
        }

        $value = null;
        foreach ($found as $val => $label) {
            $label = trim((string)$label);
            if ($label === $name) { $value = (string)$val; break; }
            if ($value === null && $label !== '' && str_contains($label, $name)) { $value = (string)$val; }
        }
        if ($value === null) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete', $name));
        }

        $this->addItemByValue($driver, $selector, $value);
    }

    /**
     * Usuń pozycję po nazwie (exact/contains).
     */
    public function removeByName(DriverInterface $driver, string $selector, string $name): void
    {
        $selected = $this->getSelectedItems($driver, $selector);
        $value = null;
        foreach ($selected as $val => $label) {
            $label = trim((string)$label);
            if ($label === $name || ($label !== '' && str_contains($label, $name))) { $value = (string)$val; break; }
        }
        if ($value === null) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" among selected items', $name));
        }

        $this->removeItemByValue($driver, $selector, $value);
    }

    /**
     * Wybierz pozycję po value (dokładny match).
     */
    public function selectByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $found = $this->search($driver, $selector, $value);
        if (!array_key_exists($value, $found)) {
            throw new \InvalidArgumentException(sprintf('Could not find "%s" in the autocomplete', $value));
        }
        $this->addItemByValue($driver, $selector, $value);
    }

    /**
     * Usuń pozycję po value.
     */
    public function removeByValue(DriverInterface $driver, string $selector, string $value): void
    {
        $selected = $this->getSelectedItems($driver, $selector);
        if (!array_key_exists($value, $selected)) {
            throw new \InvalidArgumentException(sprintf('Value "%s" is not selected', $value));
        }
        $this->removeItemByValue($driver, $selector, $value);
    }

    /**
     * Wyczyść wybór.
     */
    public function clear(DriverInterface $driver, string $selector): void
    {
        $selector = $this->escapeForJsDoubleQuoted($selector);
        $driver->executeScript(<<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                if (el && el.tomselect) { el.tomselect.clear(); el.tomselect.refreshOptions(false); }
            })();
        JS);
    }

    /* ======================== prywatne ======================== */

    private function addItemByValue(DriverInterface $driver, string $selector, int|string $value): void
    {
        $selector = $this->escapeForJsDoubleQuoted($selector);
        $val      = $this->toJsString((string)$value);

        $driver->executeScript(<<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                if (!el) return;
                var ts = el.tomselect;
                el.closest('.ts-wrapper')?.scrollIntoView({block:'center', inline:'center'});
                ts.addItem({$val});
                ts.refreshOptions(false);
                ts.close();
            })();
        JS);
    }

    private function removeItemByValue(DriverInterface $driver, string $selector, int|string $value): void
    {
        $selector = $this->escapeForJsDoubleQuoted($selector);
        $val      = $this->toJsString((string)$value);

        $driver->executeScript(<<<JS
            (function(){
                var el = document.evaluate("{$selector}", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
                if (!el) return;
                var ts = el.tomselect;
                ts.removeItem({$val});
                ts.refreshOptions(false);
            })();
        JS);
    }

    /** Ucieczka do użycia wewnątrz podwójnych cudzysłowów w JS */
    private function escapeForJsDoubleQuoted(string $s): string
    {
        return str_replace('"', '\"', $s);
    }

    /** Tworzy bezpieczny literał JS string */
    private function toJsString(string $s): string
    {
        return '"'.addslashes($s).'"';
    }
}
