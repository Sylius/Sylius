<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class IndexPage extends SymfonyPage implements IndexPageInterface
{
    /**
     * @var TableAccessorInterface
     */
    private $tableAccessor;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param TableAccessorInterface $tableAccessor
     * @param string $routeName
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor,
        $routeName
    ) {
        parent::__construct($session, $parameters, $router);

        $this->tableAccessor = $tableAccessor;
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleResourceOnPage(array $parameters)
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

    /**
     * {@inheritdoc}
     */
    public function getColumnFields($columnName)
    {
        return $this->tableAccessor->getIndexedColumn($this->getElement('table'), $columnName);
    }

    /**
     * {@inheritdoc}
     */
    public function sortBy($fieldName)
    {
        $sortableHeaders = $this->tableAccessor->getSortableHeaders($this->getElement('table'));
        Assert::keyExists($sortableHeaders, $fieldName, sprintf('Column "%s" is not sortable.', $fieldName));

        $sortableHeaders[$fieldName]->find('css', 'a')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function isSingleResourceWithSpecificElementOnPage(array $parameters, $element)
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

    /**
     * @return int
     */
    public function countItems()
    {
        try {
            return $this->getTableAccessor()->countTableBodyRows($this->getElement('table'));
        } catch (ElementNotFoundException $exception) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteResourceOnPage(array $parameters)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $deletedRow = $tableAccessor->getRowWithFields($table, $parameters);
        $actionButtons = $tableAccessor->getFieldFromRow($table, $deletedRow, 'actions');

        $actionButtons->pressButton('Delete');
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsForResource(array $parameters)
    {
        $tableAccessor = $this->getTableAccessor();
        $table = $this->getElement('table');

        $resourceRow = $tableAccessor->getRowWithFields($table, $parameters);

        return $tableAccessor->getFieldFromRow($table, $resourceRow, 'actions');
    }

    public function filter()
    {
        $this->getElement('filter')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return TableAccessorInterface
     */
    protected function getTableAccessor()
    {
        return $this->tableAccessor;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'filter' => 'button:contains("Filter")',
            'table' => '.table',
        ]);
    }
}
