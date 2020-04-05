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

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;
use Webmozart\Assert\Assert;

final class TableAccessor implements TableAccessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRowWithFields(NodeElement $table, array $fields)
    {
        try {
            return $this->getRowsWithFields($table, $fields)[0];
        } catch (\InvalidArgumentException $exception) {
            throw new \InvalidArgumentException('Could not find row with given fields', 0, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRowsWithFields(NodeElement $table, array $fields)
    {
        try {
            return $this->findRowsWithFields($table, $fields);
        } catch (\InvalidArgumentException $exception) {
            throw new \InvalidArgumentException('Could not find any row with given fields', 0, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldFromRow(NodeElement $table, NodeElement $row, $field)
    {
        $columnIndex = $this->getColumnIndex($table, $field);

        $columns = $row->findAll('css', 'td,th');
        if (!isset($columns[$columnIndex])) {
            throw new \InvalidArgumentException(sprintf('Could not find column with index %d', $columnIndex));
        }

        return $columns[$columnIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function getIndexedColumn(NodeElement $table, $fieldName)
    {
        $columnIndex = $this->getColumnIndex($table, $fieldName);

        $rows = $table->findAll('css', 'tbody > tr');
        Assert::notEmpty($rows, 'There are no rows!');

        $columnFields = [];
        /** @var NodeElement $row */
        foreach ($rows as $row) {
            $cells = $row->findAll('css', 'td');
            $columnFields[] = $cells[$columnIndex]->getText();
        }

        return $columnFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortableHeaders(NodeElement $table)
    {
        $sortableHeaders = $table->findAll('css', 'th.sortable');
        Assert::notEmpty($sortableHeaders, 'There are no sortable headers.');

        $sortableArray = [];
        /** @var NodeElement $sortable */
        foreach ($sortableHeaders as $sortable) {
            $fieldName = $this->getColumnFieldName($sortable);

            $sortableArray[$fieldName] = $sortable;
        }

        return $sortableArray;
    }

    /**
     * {@inheritdoc}
     */
    public function countTableBodyRows(NodeElement $table)
    {
        return count($table->findAll('css', 'tbody > tr'));
    }

    /**
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException If rows were not found
     */
    private function findRowsWithFields(NodeElement $table, array $fields)
    {
        $rows = $table->findAll('css', 'tr');
        Assert::notEmpty($rows, 'There are no rows!');

        $fields = $this->replaceColumnNamesWithColumnIndexes($table, $fields);

        $matchedRows = [];
        /** @var NodeElement[] $rows */
        $rows = $table->findAll('css', 'tr');
        foreach ($rows as $row) {
            /** @var NodeElement[] $columns */
            $columns = $row->findAll('css', 'td, th');
            if ($this->hasRowFields($columns, $fields)) {
                $matchedRows[] = $row;
            }
        }

        return $matchedRows;
    }

    /**
     * @return bool
     */
    private function hasRowFields(array $columns, array $fields)
    {
        foreach ($fields as $index => $searchedValue) {
            if (!isset($columns[$index])) {
                return false;
            }

            $searchedValue = (string) $searchedValue;
            $searchedValue = trim($searchedValue);

            if (0 === strpos($searchedValue, '%') && (strlen($searchedValue) - 1) === strrpos($searchedValue, '%')) {
                $searchedValue = substr($searchedValue, 1, -2);
            }

            if (!$this->containsSearchedValue($columns[$index]->getText(), $searchedValue)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[] $fields
     *
     * @return string[]
     *
     * @throws \Exception
     */
    private function replaceColumnNamesWithColumnIndexes(NodeElement $table, array $fields)
    {
        $replacedFields = [];
        foreach ($fields as $columnName => $expectedValue) {
            $columnIndex = $this->getColumnIndex($table, $columnName);

            $replacedFields[$columnIndex] = $expectedValue;
        }

        return $replacedFields;
    }

    /**
     * @param string $fieldName
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    private function getColumnIndex(NodeElement $table, $fieldName)
    {
        $rows = $table->findAll('css', 'tr');
        Assert::notEmpty($rows, 'There are no rows!');

        /** @var NodeElement $headerRow */
        $headerRow = $rows[0];
        $headers = $headerRow->findAll('css', 'th,td');

        /** @var NodeElement $column */
        foreach ($headers as $index => $column) {
            $columnFieldName = $this->getColumnFieldName($column);
            if ($fieldName === $columnFieldName) {
                return $index;
            }
        }

        throw new \InvalidArgumentException(sprintf('Column with name "%s" not found!', $fieldName));
    }

    /**
     * @param string $sourceText
     * @param string $searchedValue
     *
     * @return bool
     */
    private function containsSearchedValue($sourceText, $searchedValue)
    {
        return false !== stripos(trim($sourceText), $searchedValue);
    }

    /**
     * @return string
     */
    private function getColumnFieldName(NodeElement $column)
    {
        return preg_replace('/.*sylius-table-column-([^ ]+).*$/', '\1', $column->getAttribute('class'));
    }
}
