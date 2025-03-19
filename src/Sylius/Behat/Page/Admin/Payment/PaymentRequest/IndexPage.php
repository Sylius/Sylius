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

namespace Sylius\Behat\Page\Admin\Payment\PaymentRequest;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Symfony\Component\Routing\RouterInterface;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName,
        protected readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters, $router, $tableAccessor, $routeName);
    }

    public function choosePaymentMethodToFilter(string $paymentMethodName): void
    {
        $this->specifyAutocompleteFilter($this->getElement('filter_payment_method'), $paymentMethodName);
    }

    public function chooseActionToFilter(string $action): void
    {
        $this->getElement('filter_action')->selectOption($action);
    }

    public function chooseStateToFilter(string $state): void
    {
        $this->getElement('filter_state')->selectOption($state);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'filter_action' => '#criteria_action',
            'filter_payment_method' => '#criteria_payment_method',
            'filter_state' => '#criteria_state',
        ]);
    }

    protected function specifyAutocompleteFilter(NodeElement $autocomplete, string $value): void
    {
        if (!$this->areFiltersVisible()) {
            $this->toggleFilters();
        }

        $this->autocompleteHelper->selectByName(
            $this->getDriver(),
            $autocomplete->getXpath(),
            $value,
        );

        $this->waitForFormUpdate();
    }
}
