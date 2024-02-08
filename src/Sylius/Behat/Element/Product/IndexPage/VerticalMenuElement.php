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

namespace Sylius\Behat\Element\Product\IndexPage;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class VerticalMenuElement extends Element implements VerticalMenuElementInterface
{
    public function getMenuItems(): array
    {
        $menu = $this->getElement('vertical-menu');

        return array_map(fn (NodeElement $element): string => $element->getText(), $menu->findAll('css', '[data-test-vertical-menu-item]'));
    }

    public function canNavigateToParentTaxon(): bool
    {
        $menu = $this->getElement('vertical-menu');

        return $menu->find('css', '[data-test-vertical-menu-go-level-up]') !== null;
    }

    protected function getDefinedElements(): array
    {
        return [
            'vertical-menu' => '[data-test-vertical-menu]',
        ];
    }
}
