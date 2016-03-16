<?php

namespace Sylius\Behat;

use Behat\Mink\Element\NodeElement;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TableManipulator implements TableManipulatorInterface
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
            $foundRows = $this->findRowsWithFields($table, $fields);

            return iterator_to_array($foundRows);
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
     * @param array $fields
     *
     * @return \Generator|NodeElement[]
     *
     * @throws \InvalidArgumentException If columns or rows were not found
     */
    private function findRowsWithFields(NodeElement $table, array $fields)
    {
        $rows = $table->findAll('css', 'tr');

        if (!isset($rows[0])) {
            throw new \InvalidArgumentException('There are no rows!');
        }

        $fields = $this->replaceColumnNamesWithColumnIds($table, $fields);

        /** @var NodeElement[] $rows */
        $rows = $table->findAll('css', 'tr');
        foreach ($rows as $row) {
            /** @var NodeElement[] $columns */
            $columns = $row->findAll('css', 'th,td');
            foreach ($fields as $index => $searchedValue) {
                if (!isset($columns[$index])) {
                    throw new \InvalidArgumentException(sprintf('There is no column with index %d', $index));
                }

                $containing = false;
                $searchedValue = trim($searchedValue);
                if (0 === strpos($searchedValue, '%') && (strlen($searchedValue) - 1) === strrpos($searchedValue, '%')) {
                    $searchedValue = substr($searchedValue, 1, -2);
                    $containing = true;
                }

                $position = stripos(trim($columns[$index]->getText()), $searchedValue);
                if (($containing && false === $position) || (!$containing && 0 !== $position)) {
                    continue 2;
                }
            }

            yield $row;
        }
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
}
