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

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    public function getTotalSales(): string
    {
        return $this->getElement('total_sales')->getText();
    }

    public function getNumberOfNewOrders(): int
    {
        return (int) $this->getElement('new_orders')->getText();
    }

    public function getNumberOfNewOrdersInTheList(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('order_list'));
    }

    public function getNumberOfNewCustomers(): int
    {
        return (int) $this->getElement('new_customers')->getText();
    }

    public function getNumberOfNewCustomersInTheList(): int
    {
        return $this->tableAccessor->countTableBodyRows($this->getElement('customer_list'));
    }

    public function getAverageOrderValue(): string
    {
        return $this->getElement('average_order_value')->getText();
    }

    public function getSubHeader(): string
    {
        return trim($this->getElement('sub_header')->getText());
    }

    public function logOut(): void
    {
        $this->getElement('logout')->click();
    }

    public function chooseChannel(string $channelName): void
    {
        $this->getElement('channel_choosing_link', ['%channelName%' => $channelName])->click();
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_dashboard';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'average_order_value' => '#average-order-value',
            'customer_list' => '#customers',
            'dropdown' => 'i.dropdown',
            'logout' => '#sylius-logout-button',
            'new_customers' => '#new-customers',
            'new_orders' => '#new-orders',
            'order_list' => '#orders',
            'total_sales' => '#total-sales',
            'sub_header' => '.ui.header .content .sub.header',
            'channel_choosing_link' => 'a:contains("%channelName%")',
        ]);
    }
}
