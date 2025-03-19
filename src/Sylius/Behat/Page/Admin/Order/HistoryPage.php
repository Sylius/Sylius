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

namespace Sylius\Behat\Page\Admin\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class HistoryPage extends SymfonyPage implements HistoryPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_order_history';
    }

    public function countBillingAddressChanges(): int
    {
        return count($this->getElement('billing_address_logs')->findAll('css', '[data-test-address-log]'));
    }

    public function countShippingAddressChanges(): int
    {
        return count($this->getElement('shipping_address_logs')->findAll('css', '[data-test-address-log]'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address_logs' => '[data-test-address-type="Billing address"]',
            'shipping_address_logs' => '[data-test-address-type="Shipping address"]',
        ]);
    }
}
