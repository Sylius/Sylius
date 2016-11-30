<?php

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface TableAccessorInterface
{
    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return NodeElement
     *
     * @throws \InvalidArgumentException If row cannot be found
     */
    public function getRowWithFields(NodeElement $table, array $fields);

    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException If there is no rows fulfilling given conditions
     */
    public function getRowsWithFields(NodeElement $table, array $fields);

    /**
     * @param NodeElement $table
     * @param string $fieldName
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getIndexedColumn(NodeElement $table, $fieldName);

    /**
     * @param NodeElement $table
     *
     * @return NodeElement[]
     */
    public function getSortableHeaders(NodeElement $table);

    /**
     * @param NodeElement $table
     * @param NodeElement $row
     * @param string $field
     *
     * @return NodeElement
     */
    public function getFieldFromRow(NodeElement $table, NodeElement $row, $field);

    /**
     * @param NodeElement $table
     *
     * @return int
     */
    public function countTableBodyRows(NodeElement $table);
}
