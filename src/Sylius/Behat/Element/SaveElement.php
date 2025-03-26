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

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;

class SaveElement extends Element implements SaveElementInterface
{
    public function saveChanges(): void
    {
        try {
            $this->getElement('update_changes_button')->press();
        } catch (ElementNotFoundException) {
            // Fallback for elements with different data-test attributes
            $this->getElement('save_changes_button')->press();
        }
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'save_changes_button' => '[data-test-button="save-changes"]',
            'update_changes_button' => '[data-test-update-changes-button]',
        ]);
    }
}
