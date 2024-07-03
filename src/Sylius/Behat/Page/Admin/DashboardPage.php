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
use Sylius\Component\Core\Model\ProductInterface;
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
    public function getNumberOfPaidOrders(): int
    {
        return (int) $this->getElement('paid_orders')->getText();
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
        $this->getElement('channel_choosing_button')->click();
        $this->getElement('channel_choosing_list', ['%channelName%' => $channelName])->click();
        $this->waitForStatisticsUpdate();
    }

    /** @throws ElementNotFoundException */
    public function chooseYearSplitByMonthsInterval(): void
    {
        $this->getElement('year_split_by_months_statistics_button')->click();
        $this->waitForStatisticsUpdate();
    }

    /** @throws ElementNotFoundException */
    public function chooseMonthSplitByDaysInterval(): void
    {
        $this->getElement('month_split_by_days_statistics_button')->click();
    }

    /** @throws ElementNotFoundException */
    public function choosePreviousPeriod(): void
    {
        $this->getElement('previous_period')->click();
        $this->waitForStatisticsUpdate();
    }

    /** @throws ElementNotFoundException */
    public function chooseNextPeriod(): void
    {
        $this->getElement('next_period')->click();
        $this->waitForStatisticsUpdate();
    }

    public function searchForProductViaNavbar(ProductInterface $productName): void
    {
        $form = $this->getElement('product_navbar_search');
        $form->find('css', 'input')->setValue($productName);
        $form->find('css', 'button')->click();
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
            'average_order_value' => '[data-test-average-order-value]',
            'channel_choosing_button' => '[data-test-choose-channel-button]',
            'channel_choosing_list' => '[data-test-choose-channel-list] a:contains("%channelName%")',
            'customer_list' => '#customers',
            'dropdown' => 'i.dropdown',
            'logout' => '[data-test-user-dropdown-item="Logout"]',
            'month_split_by_days_statistics_button' => 'button[data-stats-button="month"]',
            'new_customers' => '[data-test-new-customers]',
            'next_period' => '[data-test-next-period]',
            'order_list' => '[data-test-new-orders]',
            'paid_orders' => '[data-test-paid-orders]',
            'previous_period' => '[data-test-previous-period]',
            'product_navbar_search' => '[data-test-navbar-product-search]',
            'statistics_component' => '[data-test-statistics-component]',
            'sub_header' => '.ui.header .content .sub.header',
            'total_sales' => '[data-test-total-sales]',
            'year_split_by_months_statistics_button' => '[data-test-year-split-into-months]',
        ]);
    }

    private function waitForStatisticsUpdate(): void
    {
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $liveElement = $this->getElement('statistics_component');
        $liveElement->waitFor(2500, fn () => !$liveElement->hasAttribute('busy'));
    }
}
