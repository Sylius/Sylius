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

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Component\Customer\Model\CustomerInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function getCustomerAccountStatus(CustomerInterface $customer): string
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);

        return $tableAccessor->getFieldFromRow($table, $row, 'enabled')->getText();
    }

    public function isCustomerVerified(CustomerInterface $customer): bool
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);

        return $tableAccessor->getFieldFromRow($table, $row, 'verified')->getText() === 'Yes';
    }

    public function specifyFilterGroup(string $groupName): void
    {
        $groupFilterElement = $this->getElement('filter_group')->getParent();

        AutocompleteHelper::chooseValue($this->getSession(), $groupFilterElement, $groupName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_group' => '#criteria_group',
        ]);
    }
}
