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
    /** @var TableAccessorInterface */
    private $tableAccessor;

    /** @var string */
    private $routeName;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        string $routeName
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
        $this->routeName = $routeName;
    }

    public function isSingleResourceOnPage(array $parameters): bool
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields($this->getElement('table'), $parameters);

            return 1 === count($rows);
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    public function getColumnFields(string $columnName): array
    {
        return $this->tableAccessor->getIndexedColumn($this->getElement('table'), $columnName);
    }

    public function sortBy(string $fieldName): void
    {
        $sortableHeaders = $this->tableAccessor->getSortableHeaders($this->getElement('table'));
        Assert::keyExists($sortableHeaders, $fieldName, sprintf('Column "%s" is not sortable.', $fieldName));

        $sortableHeaders[$fieldName]->find('css', 'a')->click();
    }

    public function isSingleResourceWithSpecificElementOnPage(array $parameters, string $element): bool
    {
        try {
            $rows = $this->tableAccessor->getRowsWithFields($this->getElement('table'), $parameters);

            if (1 !== count($rows)) {
                return false;
            }

            return null !== $rows[0]->find('css', $element);
        } catch (\InvalidArgumentException $exception) {
            return false;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    public function countItems(): int
    {
        try {
            return $this->getTableAccessor()->countTableBodyRows($this->getElement('table'));
        } catch (ElementNotFoundException $exception) {
            return 0;
        }
    }

    public function deleteResourceOnPage(array $parameters): void
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $deletedRow = $tableAccessor->getRowWithFields($table, $parameters);
        $actionButtons = $tableAccessor->getFieldFromRow($table, $deletedRow, 'actions');

        $actionButtons->pressButton('Delete');
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
        $bulkCheckbox = $resourceRow->find('css', '.bulk-select-checkbox');

        Assert::notNull($bulkCheckbox);

        $bulkCheckbox->check();
    }

    public function filter(): void
    {
        $this->getElement('filter')->press();
    }

    public function bulkDelete(): void
    {
        $this->getElement('bulk_actions', ['%text%' => 'Bulk actions'])->pressButton('Delete');
        $this->getElement('confirmation_button')->click();
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    protected function getTableAccessor(): TableAccessorInterface
    {
        return $this->tableAccessor;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'bulk_actions' => '.accordion:contains("%text%")',
            'confirmation_button' => '#confirmation-button',
            'filter' => 'button:contains("Filter")',
            'table' => '.table',
        ]);
    }
}
