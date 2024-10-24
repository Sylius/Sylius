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

use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        private AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);
    }

    public function isCustomerEnabled(CustomerInterface $customer): bool
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);
        $enabledField = $tableAccessor->getFieldFromRow($table, $row, 'enabled');

        return $enabledField->has('css', '[data-test-status-enabled]') ? true : false;
    }

    public function isCustomerVerified(CustomerInterface $customer): bool
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $row = $tableAccessor->getRowWithFields($table, ['email' => $customer->getEmail()]);
        $verifiedField = $tableAccessor->getFieldFromRow($table, $row, 'verified');

        return $verifiedField->has('css', '[data-test-status-enabled]') ? true : false;
    }

    public function setFilterGroup(string $groupName): void
    {
        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $this->getElement('filter_group')->getXpath(),
            $groupName,
        );

        $this->waitForFormUpdate();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_group' => '#criteria_group',
        ]);
    }
}
