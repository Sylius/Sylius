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

    public function isSectionWithLabelVisible(string $name): bool;

    public function logOut(): void;

    public function chooseChannel(string $channelName): void;

    public function chooseYearSplitByMonthsInterval(): void;

    public function chooseMonthSplitByDaysInterval(): void;

    public function choosePreviousPeriod(): void;

    public function chooseNextPeriod(): void;
}
