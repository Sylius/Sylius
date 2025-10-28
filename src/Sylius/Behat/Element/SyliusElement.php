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

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element as BaseElement;
use Sylius\Behat\Service\DriverHelper;

abstract class SyliusElement extends BaseElement
{
    /** @param array<string, mixed> $parameters */
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        $this->waitForLiveComponentToFinish();

        return parent::getElement($name, $parameters);
    }

    protected function waitForLiveComponentToFinish(): void
    {
        if (DriverHelper::isJavascript($this->getDriver()) === false) {
            return;
        }

        // Wait for ALL LiveComponents to complete their operations
        // Returns immediately if condition is already met
        // Max 10 seconds for: DOM ready, no loading indicators, no busy elements
        $this->getSession()->wait(
            10000,
            "document.readyState === 'complete' && " .
            "document.querySelectorAll('[data-live-is-loading]').length === 0 && " .
            "document.querySelectorAll('[busy]').length === 0",
        );
    }
}
