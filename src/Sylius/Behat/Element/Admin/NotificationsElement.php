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

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\DriverHelper;

final class NotificationsElement extends Element implements NotificationsElementInterface
{
    public function hasNotification(string $type, string $message): bool
    {
        $flashesContainer = $this->getElement('flashes_container');

        if (DriverHelper::isJavascript($this->getDriver())) {
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
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'flashes_container' => '[data-test-sylius-flashes-container]',
        ]);
    }
}
