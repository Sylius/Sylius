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

namespace Sylius\Behat\Element\Admin\ProductAttribute;

use FriendsOfBehat\PageObjectExtension\Element\Element;

class FilterElement extends Element implements FilterElementInterface
{
    public function chooseType(string $type): void
    {
        $this->getElement('filter_type')->selectOption($type, true);
    }

    public function chooseTranslatable(string $translatable): void
    {
        $this->getElement('filter_translatable')->selectOption($translatable);
    }

    public function filter(): void
    {
        $this->getElement('filter_button')->press();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_button' => '[data-test-filter]',
            'filter_translatable' => '#criteria_translatable',
            'filter_type' => '#criteria_type',
        ]);
    }
}
