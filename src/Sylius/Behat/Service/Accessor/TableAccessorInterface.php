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
