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

namespace Sylius\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface DashboardPageInterface extends SymfonyPageInterface
{
    public function getTotalSales(): string;

    public function getNumberOfNewOrders(): int;

    public function getNumberOfNewOrdersInTheList(): int;

    public function getNumberOfNewCustomers(): int;

    public function getNumberOfNewCustomersInTheList(): int;

    public function getAverageOrderValue(): string;

    public function getSubHeader(): string;

    public function logOut(): void;

    public function chooseChannel(string $channelName): void;
}
