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

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private TableAccessorInterface $tableAccessor,
        private string $routeName,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function isSingleResourceOnPage(array $parameters): bool
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields($this->getElement('table'), $parameters);

            return 1 === count($rows);
        } catch (ElementNotFoundException|\InvalidArgumentException) {
            return false;
        }
    }

    public function getColumnFields(string $columnName): array
    {
        return $this->tableAccessor->getIndexedColumn($this->getElement('table'), $columnName);
    }

    public function sortBy(string $fieldName, ?string $order = null): void
    {
        $sortableHeaders = $this->tableAccessor->getSortableHeaders($this->getElement('table'));
        Assert::keyExists($sortableHeaders, $fieldName, sprintf('Column "%s" does not exist or is not sortable.', $fieldName));

        /** @var NodeElement $sortingHeader */
        $sortingHeader = $sortableHeaders[$fieldName]->find('css', 'a');
        preg_match('/\?sorting[^=]+\=([acdes]+)/i', $sortingHeader->getAttribute('href'), $matches);
        $nextSortingOrder = $matches[1] ?? 'desc';

        $sortableHeaders[$fieldName]->find('css', 'a')->click();

        if (null !== $order && ($order !== $nextSortingOrder)) {
            $sortableHeaders[$fieldName]->find('css', 'a')->click();
        }
    }

    public function isSingleResourceWithSpecificElementOnPage(array $parameters, string $element): bool
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields($this->getElement('table'), $parameters);

            if (1 !== count($rows)) {
                return false;
            }

            return null !== $rows[0]->find('css', $element);
        } catch (ElementNotFoundException|\InvalidArgumentException) {
            return false;
        }
    }

    public function countItems(): int
    {
        try {
            return $this->getTableAccessor()->countTableBodyRows($this->getElement('table'));
        } catch (ElementNotFoundException) {
            return 0;
        }
    }

    public function deleteResourceOnPage(array $parameters): void
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $deletedRow = $tableAccessor->getRowWithFields($table, $parameters);
        $actionButtons = $tableAccessor->getFieldFromRow($table, $deletedRow, 'actions');

        $actionButtons->pressButton('delete');
    }

    public function getActionsForResource(array $parameters): NodeElement
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $resourceRow = $tableAccessor->getRowWithFields($table, $parameters);

        return $tableAccessor->getFieldFromRow($table, $resourceRow, 'actions');
    }

    public function checkResourceOnPage(array $parameters): void
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $resourceRow = $tableAccessor->getRowWithFields($table, $parameters);
        $bulkCheckbox = $resourceRow->find('css', '.form-check-input');

        Assert::notNull($bulkCheckbox);

        $bulkCheckbox->check();
    }

    public function filter(): void
    {
        $this->getElement('filter')->press();
    }

    public function bulkDelete(): void
    {
        $this->getElement('bulk_actions')->pressButton('Delete');
        $this->getElement('confirmation_button')->click();
    }

    public function sort(string $order): void
    {
        $this->getDocument()->clickLink($order);
    }

    public function chooseEnabledFilter(): void
    {
        $this->getElement('enabled_filter')->selectOption('Yes');
    }

    public function isEnabledFilterApplied(): bool
    {
        return $this->getElement('enabled_filter')->getValue() === 'true';
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    protected function getTableAccessor(): TableAccessorInterface
    {
        return $this->tableAccessor;
    }

    protected function toggleFilters(): void
    {
        $filtersToggle = $this->getElement('filters_toggle');
        $filtersToggle->click();
        $this->getDocument()->waitFor(1, function () use ($filtersToggle) {
            $accordionCollapse = $filtersToggle->find('css', '.accordion-collapse');

            return null !== $accordionCollapse && !$accordionCollapse->hasClass('collapsing');
        });
    }

    protected function areFiltersVisible(): bool
    {
        return !$this->getElement('filters_toggle')->hasClass('collapsed');
    }

    protected function waitForFormUpdate(): void
    {
        $form = $this->getElement('filters_form');
        usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'bulk_actions' => '.sylius-grid-nav__bulk',
            'confirmation_button' => '[data-confirm-btn-true]',
            'enabled_filter' => '#criteria_enabled',
            'filter' => '[data-test-filter]',
            'filters_form' => '[data-test-filters-form]',
            'filters_toggle' => '.accordion-button',
            'table' => '.table',
        ]);
    }
}
