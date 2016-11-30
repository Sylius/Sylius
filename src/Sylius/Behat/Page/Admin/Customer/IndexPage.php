<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCustomerAccountStatus($customer)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);

        return $tableAccessor->getFieldFromRow($table, $row, 'enabled')->getText();
    }
}
