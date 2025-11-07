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

namespace Sylius\Behat\Element;

use Behat\Mink\Element\NodeElement as BaseNodeElement;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Sylius\Behat\Behaviour\WaitsForElements;

/**
 * NodeElement wrapper that waits for LiveComponent after click.
 */
class NodeElement extends BaseNodeElement
{
    use WaitsForElements;

    /**
     * Clicks element and waits for LiveComponent (handles delayed [busy]).
     */
    public function click(): void
    {
        try {
            parent::click();
        } catch (UnsupportedDriverActionException) {
            // In non-JS (SymfonyDriver), only certain elements are clickable
        }

        if ($this->isJavascript()) {
            // JS scenarios: trigger click and wait for LiveComponent

            // Wait for LiveComponent to start processing (busy/loading indicators appear)
            // This is more reliable than fixed sleep - we poll until busy appears OR timeout
            $start = microtime(true);
            $this->getSession()->wait(1000, "document.querySelector('[busy], [data-live-is-loading]')");
            $waitForBusy = round((microtime(true) - $start) * 1000, 2);

            if ($waitForBusy > 50) {
                error_log(sprintf('[CLICK-WAIT] Waited %.2f ms for LiveComponent to start', $waitForBusy));
            }

            // Now wait for all busy/loading indicators to disappear
            $this->waitForLiveComponentToFinish();
        }
    }

    public function press(): void
    {
        if ($this->isJavascript()) {
            // For press() (form submission), manually trigger the press without our click() override
            $this->getDriver()->click($this->getXpath());

            // Wait a bit for navigation to start
            usleep(200000); // 200ms

            // Wait for new page to fully load

            return;
        }

        parent::press();
    }

    public function setValue($value)
    {
        parent::setValue($value);

        if ($this->isJavascript()) {
            // Wait for LiveComponent to react to value change (max 200ms)
            $start = microtime(true);
            $this->getSession()->wait(200, "document.querySelector('[busy], [data-live-is-loading]')");
            $waitForBusy = round((microtime(true) - $start) * 1000, 2);

            if ($waitForBusy > 10) {
                error_log(sprintf('[SETVALUE-WAIT] Waited %.2f ms for LiveComponent to react', $waitForBusy));
            }

            // Wait for LiveComponent to finish any loading triggered by value change
            $this->waitForLiveComponentToFinish();
        }
    }

    public function selectOption($option, $multiple = false)
    {
        parent::selectOption($option, $multiple);

        if ($this->isJavascript()) {
            // Wait for LiveComponent to react to option selection (max 200ms)
            $start = microtime(true);
            $this->getSession()->wait(200, "document.querySelector('[busy], [data-live-is-loading]')");
            $waitForBusy = round((microtime(true) - $start) * 1000, 2);

            if ($waitForBusy > 10) {
                error_log(sprintf('[SELECTOPTION-WAIT] Waited %.2f ms for LiveComponent to react', $waitForBusy));
            }

            // Wait for LiveComponent to finish any loading triggered by option selection
            $this->waitForLiveComponentToFinish();
        }
    }

    /**
     * Override find() to return our NodeElement wrapper instead of base NodeElement.
     */
    public function find($selector, $locator)
    {
        $element = parent::find($selector, $locator);

        if (null === $element) {
            return null;
        }

        return new self($element->getXpath(), $this->getSession());
    }

    /**
     * Override findAll() to return our NodeElement wrappers instead of base NodeElements.
     *
     * @return self[]
     */
    public function findAll($selector, $locator): array
    {
        $elements = parent::findAll($selector, $locator);

        return array_map(
            fn (BaseNodeElement $element) => new self($element->getXpath(), $this->getSession()),
            $elements,
        );
    }

    /**
     * Override getParent() to return our NodeElement wrapper instead of base NodeElement.
     */
    public function getParent()
    {
        $parent = parent::getParent();

        if (null === $parent) {
            return null;
        }

        return new self($parent->getXpath(), $this->getSession());
    }
}
