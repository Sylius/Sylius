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

namespace Sylius\Behat\Page;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage as BaseSymfonyPage;
use Sylius\Behat\Service\DriverHelper;

abstract class SymfonyPage extends BaseSymfonyPage implements SymfonyPageInterface
{
    protected function getElement(string $name, array $parameters = []): NodeElement
    {
        // Strategy: Fast path with lazy retry - all in one place!
        // Try 1: Get element immediately (0ms wait) - works when page is stable ✅
        // Try 2: If fails, wait for Live Components and retry
        // Try 3: If still fails, give extra 300ms and final attempt

        $lastException = null;

        // Attempt 1: Fast path (no wait)
        try {
            return parent::getElement($name, $parameters);
        } catch (\Exception $e) {
            $lastException = $e;
        }

        // Attempt 2: Wait for Live Component updates
        DriverHelper::waitForLiveComponentUpdate($this->getSession());
        try {
            return parent::getElement($name, $parameters);
        } catch (\Exception $e) {
            $lastException = $e;
        }

        // Attempt 3: Extra wait + final attempt
        if (DriverHelper::isJavascript($this->getDriver())) {
            usleep(300000); // 300ms additional wait
        }

        return parent::getElement($name, $parameters);
    }

    protected function blur(): void
    {
        $this->getDocument()->find('css', 'body')->click();
    }
}
