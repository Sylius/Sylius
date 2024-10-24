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

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;

interface TableAccessorInterface
{
    /**
     * @return NodeElement
     *
     * @throws \InvalidArgumentException If row cannot be found
     */
    public function getRowWithFields(NodeElement $table, array $fields);

    /**
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException If there is no rows fulfilling given conditions
     */
    public function getRowsWithFields(NodeElement $table, array $fields);

    /**
     * @param string $fieldName
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getIndexedColumn(NodeElement $table, $fieldName);

    /**
     * @return NodeElement[]
     */
    public function getSortableHeaders(NodeElement $table);

    /**
     * @param string $field
     *
     * @return NodeElement
     */
    public function getFieldFromRow(NodeElement $table, NodeElement $row, $field);

    /**
     * @return int
     */
    public function countTableBodyRows(NodeElement $table);
}
