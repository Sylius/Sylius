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

class NodeElement extends BaseNodeElement
{
    use WaitsForElements;

    public function click(): void
    {
        try {
            parent::click();
        } catch (UnsupportedDriverActionException) {
            // In non-JS (SymfonyDriver), only certain elements are clickable
        }

        // JS scenarios: trigger click and wait for LiveComponent
        if ($this->isJavascript()) {
            $this->waitForLiveComponentToFinish();
        }
    }

    public function press(): void
    {
        if ($this->isJavascript()) {
            // For press() (form submission), manually trigger the press without our click() override
            $this->getDriver()->click($this->getXpath());

            return;
        }

        parent::press();
    }

    public function setValue($value)
    {
        parent::setValue($value);

        if ($this->isJavascript()) {
            $this->waitForLiveComponentToFinish();
        }
    }

    public function selectOption($option, $multiple = false): void
    {
        parent::selectOption($option, $multiple);

        if ($this->isJavascript()) {
            $this->waitForLiveComponentToFinish();
        }
    }

    public function find($selector, $locator)
    {
        $element = parent::find($selector, $locator);

        if (null === $element) {
            return null;
        }

        return new self($element->getXpath(), $this->getSession());
    }

    public function findAll($selector, $locator): array
    {
        $elements = parent::findAll($selector, $locator);

        return array_map(
            fn (BaseNodeElement $element) => new self($element->getXpath(), $this->getSession()),
            $elements,
        );
    }

    public function getParent()
    {
        $parent = parent::getParent();

        if (null === $parent) {
            return null;
        }

        return new self($parent->getXpath(), $this->getSession());
    }
}
