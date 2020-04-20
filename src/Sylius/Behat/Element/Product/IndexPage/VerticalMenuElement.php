<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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

        return array_map(function (NodeElement $element): string {
            return $element->getText();
        }, $menu->findAll('css', '[data-test-vertical-menu-item]'));
    }

    protected function getDefinedElements(): array
    {
        return [
            'vertical-menu' => '[data-test-vertical-menu]',
        ];
    }
}
