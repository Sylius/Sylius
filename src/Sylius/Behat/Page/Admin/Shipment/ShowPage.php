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

namespace Sylius\Behat\Page\Admin\Shipment;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_shipment_show';
    }

    public function getAmountOfUnits(string $productName): int
    {
        $items = $this->getElement('items');

        return count($items->findAll('css', sprintf('[data-test-item="%s"]', $productName)));
    }

    public function getState(): string
    {
        return $this->getElement('state')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'items' => '[data-test-table-items]',
            'state' => '[data-test-shipment-state]',
        ]);
    }
}
