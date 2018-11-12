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
     * @throws \InvalidArgumentException If row cannot be found
     */
    public function getRowWithFields(NodeElement $table, array $fields): NodeElement;

    /**
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException If there is no rows fulfilling given conditions
     */
    public function getRowsWithFields(NodeElement $table, array $fields): array;

    /**
     * @throws \InvalidArgumentException
     */
    public function getIndexedColumn(NodeElement $table, string $fieldName): array;

    /**
     * @return NodeElement[]
     */
    public function getSortableHeaders(NodeElement $table): array;

    public function getFieldFromRow(NodeElement $table, NodeElement $row, string $field): NodeElement;

    public function countTableBodyRows(NodeElement $table): int;
}
