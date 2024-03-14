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

    public function countShippingAddressChanges(): int
    {
        return count($this->getDocument()->findAll('css', '#shipping-address-changes tbody tr'));
    }

    public function countBillingAddressChanges(): int
    {
        return count($this->getDocument()->findAll('css', '#billing-address-changes tbody tr'));
    }
}
