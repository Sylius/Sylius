<?php

namespace Sylius\Behat;

use Behat\Mink\Element\NodeElement;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface TableManipulatorInterface
{
    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return array
     *
     * @throws \InvalidArgumentException If row cannot be found
     */
    public function getRowWithFields(NodeElement $table, array $fields);

    /**
     * @param NodeElement $table
     * @param array $fields
     *
     * @return array
     *
     * @throws \InvalidArgumentException If there is no rows fulfilling given conditions
     */
    public function getRowsWithFields(NodeElement $table, array $fields);
}
