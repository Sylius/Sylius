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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface HistoryPageInterface extends SymfonyPageInterface
{
    public function countShippingAddressChanges(): int;

    public function countBillingAddressChanges(): int;
}
