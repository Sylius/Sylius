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

namespace Sylius\Behat\Element\Shop;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Element\Element;

final class MenuElement extends Element implements MenuElementInterface
{
    public function getMenuItems(): array
    {
        $menu = $this->getElement('menu');

        return array_map(fn (NodeElement $element): string => $element->getAttribute('data-test-menu-item'), $menu->findAll('css', '[data-test-menu-item]'));
    }

    protected function getDefinedElements(): array
    {
        return [
            'menu' => '[data-test-menu]',
        ];
    }
}
