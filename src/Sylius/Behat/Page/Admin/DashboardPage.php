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

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue>|\ArrayAccess<TKey, TValue> $minkParameters
     */
    public function __construct(
        Session $session,
        array|\ArrayAccess $minkParameters,
        RouterInterface $router,
        protected TableAccessorInterface $tableAccessor,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    /** @throws ElementNotFoundException */
    public function getTotalSales(): string
    {
        return $this->getElement('total_sales')->getText();
    }

    /** @throws ElementNotFoundException */
    public function getNumberOfNewOrders(): int
    {
        return (int) $this->getElement('new_orders')->getText();
    }

    /** @throws ElementNotFoundException */
    public function getNumberOfNewOrdersInTheList(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_list'));
    }

    /** @throws ElementNotFoundException */
    public function getNumberOfNewCustomers(): int
    {
        return (int) $this->getElement('new_customers')->getText();
    }

    /** @throws ElementNotFoundException */
    public function getNumberOfNewCustomersInTheList(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_list'));
    }

    /** @throws ElementNotFoundException */
    public function getAverageOrderValue(): string
    {
        return $this->getElement('average_order_value')->getText();
    }

    /** @throws ElementNotFoundException */
    public function getSubHeader(): string
    {
        return trim($this->getElement('sub_header')->getText());
    }

    /** @throws ElementNotFoundException */
    public function isSectionWithLabelVisible(string $name): bool
    {
        return $this->getElement('admin_menu')->find('css', sprintf('div:contains(%s)', $name)) !== null;
    }

    /** @throws ElementNotFoundException */
    public function logOut(): void
    {
        $this->getElement('logout')->click();
    }

    /** @throws ElementNotFoundException */
    public function chooseChannel(string $channelName): void
    {
        $this->getElement('channel_choosing_link', ['%channelName%' => $channelName])->click();
    }

    /** @throws ElementNotFoundException */
    public function chooseYearSplitByMonthsInterval(): void
    {
        $this->getElement('year_split_by_months_statistics_button')->click();
    }

    /** @throws ElementNotFoundException */
    public function chooseMonthSplitByDaysInterval(): void
    {
        $this->getElement('month_split_by_days_statistics_button')->click();
    }

    /** @throws ElementNotFoundException */
    public function choosePreviousPeriod(): void
    {
        $this->getElement('navigation_previous')->click();

        usleep(500000);
    }

    /** @throws ElementNotFoundException */
    public function chooseNextPeriod(): void
    {
        $this->getElement('navigation_next')->click();

        usleep(500000);
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_dashboard';
    }

    /** @return array<string, string> */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'admin_menu' => '.sylius-admin-menu',
            'average_order_value' => '#average-order-value',
            'channel_choosing_link' => 'a:contains("%channelName%")',
            'customer_list' => '#customers',
            'dropdown' => 'i.dropdown',
            'logout' => '#sylius-logout-button',
            'month_split_by_days_statistics_button' => 'button[data-stats-button="month"]',
            'navigation_next' => '#navigation-next',
            'navigation_previous' => '#navigation-prev',
            'new_customers' => '#new-customers',
            'new_orders' => '#new-orders',
            'order_list' => '#orders',
            'sub_header' => '.ui.header .content .sub.header',
            'total_sales' => '#total-sales',
            'year_split_by_months_statistics_button' => 'button[data-stats-button="year"]',
        ]);
    }
}
