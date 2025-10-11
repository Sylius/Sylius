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
        // Strategy: Fast path with lazy retry
        // Try 1: Get element immediately (0ms wait) - works when page is stable
        // Try 2-3: If fails, wait for Live Components and retry

        try {
            return parent::getElement($name, $parameters);
        } catch (\Exception $e) {
            // First failure - wait for any Live Component updates
            DriverHelper::waitForLiveComponentUpdate($this->getSession());

            try {
                return parent::getElement($name, $parameters);
            } catch (\Exception $e2) {
                // Second failure - give it one more short wait
                if (DriverHelper::isJavascript($this->getDriver())) {
                    usleep(300000); // 300ms additional wait
                }

                // Final attempt - let it throw if still fails
                return parent::getElement($name, $parameters);
            }
        }
    }

    /**
     * Get element text with retry for Live Component updates.
     * Use this when reading values that may be updated by AJAX (statistics, counts, etc.)
     *
     * @param callable $callback Function that returns the value to read (e.g., fn() => $this->getElement('count')->getText())
     * @param int $maxRetries Maximum number of retries (default 2)
     */
    protected function getWithRetry(callable $callback, int $maxRetries = 2): mixed
    {
        $lastException = null;

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;

                if ($attempt < $maxRetries) {
                    // Wait for Live Component update before retry
                    DriverHelper::waitForLiveComponentUpdate($this->getSession());

                    if ($attempt === $maxRetries - 1 && DriverHelper::isJavascript($this->getDriver())) {
                        // Last retry - extra wait
                        usleep(300000);
                    }
                }
            }
        }

        throw $lastException;
    }

    protected function blur(): void
    {
        $this->getDocument()->find('css', 'body')->click();
    }
}
