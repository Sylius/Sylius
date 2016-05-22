<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardPage extends SymfonyPage implements DashboardPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTotalSales()
    {
        return $this->getElement('total_sales')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewOrders()
    {
        return $this->getElement('new_orders')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfNewCustomers()
    {
        return $this->getElement('new_customers')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAverageOrderValue()
    {
        return $this->getElement('average_order_value')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_dashboard';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'total_sales' => '#total-sales',
            'new_orders' => '#new-orders',
            'new_customers' => '#new-customers',
            'average_order_value' => '#average-order-value',
        ]);
    }
}
