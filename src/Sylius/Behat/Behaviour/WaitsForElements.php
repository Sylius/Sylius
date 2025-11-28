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
use Sylius\Behat\Element\NodeElement;

trait WaitsForElements
{
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        if ($this->isJavascript()) {
            $this->waitForPageToLoad();
        }

        return new NodeElement(parent::getElement($name, $parameters)->getXpath(), $this->getSession());
    }

    protected function waitForPageToLoad(): void
    {
        $this
            ->getSession()
            ->wait(1, "document.readyState === 'complete' && !document.querySelector('[data-live-is-loading]')")
        ;
    }

    protected function waitForLiveComponentToFinish(): void
    {
        if (!$this->isJavascript()) {
            return;
        }

        $this->getSession()->wait(
            1,
            "document.readyState === 'complete' && " .
            "document.querySelectorAll('[data-live-is-loading]').length === 0 && " .
            "document.querySelectorAll('[busy]').length === 0",
        );
    }

    protected function isJavascript(): bool
    {
        $driver = $this->getDriver();

        return $driver instanceof Selenium2Driver || $driver instanceof ChromeDriver || $driver instanceof PantherDriver;
    }
}
