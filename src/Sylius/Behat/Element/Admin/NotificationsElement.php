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

namespace Sylius\Behat\Element\Admin;

use Sylius\Behat\Element\NodeElement;
use Sylius\Behat\Element\SyliusElement;

class NotificationsElement extends SyliusElement implements NotificationsElementInterface
{
    public function hasNotification(string $type, string $message): bool
    {
        // Use parent::getElement to get element without waiting (it's already waited by SyliusElement)
        // Then wait for it to be visible explicitly
        try {
            $flashesContainer = parent::getElement('flashes_container');

            if ($this->isJavascript()) {
                $flashesContainer->waitFor(5, function () use ($flashesContainer) {
                    return $flashesContainer->isVisible();
                });
            }

            /** @var array<NodeElement> $flashes */
            $flashes = $flashesContainer->findAll('css', '[data-test-sylius-flash-message]');

            foreach ($flashes as $flash) {
                if (str_contains($flash->getText(), $message) && $flash->getAttribute('data-test-sylius-flash-message-type') === $type) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            // If flashes container is not found, return false
            return false;
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'flashes_container' => '[data-test-sylius-flashes-container]',
        ]);
    }
}
