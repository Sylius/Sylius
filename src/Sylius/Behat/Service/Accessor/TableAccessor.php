<?php

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
     * @param NodeElement $table
     *
     * @return int
     */
    public function countTableBodyRows(NodeElement $table)
    {
        return count($table->findAll('css', 'tbody > tr'));
    }

    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException If rows were not found
     */
    private function findRowsWithFields(NodeElement $table, array $fields)
    {
        $rows = $table->findAll('css', 'tr');
        $matchedRows = [];

        if (!isset($rows[0])) {
            throw new \InvalidArgumentException('There are no rows!');
        }

        $fields = $this->replaceColumnNamesWithColumnIds($table, $fields);

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
     * @param array $columns
     * @param array $fields
     *
     * @return bool
     */
    private function hasRowFields(array $columns, array $fields)
    {
        foreach ($fields as $index => $searchedValue) {
            if (!isset($columns[$index])) {
                return false;
            }

            $searchedValue = trim($searchedValue);

            if (0 === strpos($searchedValue, '%') && (strlen($searchedValue) - 1) === strrpos($searchedValue, '%')) {
                $searchedValue = substr($searchedValue, 1, -2);
            }

            return $this->containsSearchedValue($columns[$index]->getText(), $searchedValue);
        }

        return false;
    }

    /**
     * @param NodeElement $table
     * @param string[] $fields
     *
     * @return string[]
     *
     * @throws \Exception
     */
    private function replaceColumnNamesWithColumnIds(NodeElement $table, array $fields)
    {
        $replacedFields = [];
        foreach ($fields as $columnName => $expectedValue) {
            $columnIndex = $this->getColumnIndex($table, $columnName);

            $replacedFields[$columnIndex] = $expectedValue;
        }

        return $replacedFields;
    }

    /**
     * @param NodeElement $table
     * @param string $columnName
     *
     * @return int
     *
     * @throws \Exception If column was not found
     */
    private function getColumnIndex(NodeElement $table, $columnName)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \InvalidArgumentException('There are no rows!');
        }

        /** @var NodeElement $firstRow */
        $firstRow = $rows[0];
        $columns = $firstRow->findAll('css', 'th,td');
        foreach ($columns as $index => $column) {
            /** @var NodeElement $column */
            if (0 === stripos($column->getText(), $columnName)) {
                return $index;
            }
        }

        throw new \InvalidArgumentException(sprintf('Column with name "%s" not found!', $columnName));
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
}
