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

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isSingleResourceOnPage(array $parameters);

    /**
     * @param array $parameters
     * @param string $element
     *
     * @return bool
     */
    public function isSingleResourceWithSpecificElementOnPage(array $parameters, $element);

    /**
     * @param string $columnName
     *
     * @return array
     */
    public function getColumnFields($columnName);

    /**
     * @param string $fieldName
     */
    public function sortBy($fieldName);

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function deleteResourceOnPage(array $parameters);

    /**
     * @param array $parameters
     *
     * @return NodeElement
     */
    public function getActionsForResource(array $parameters);

    /**
     * @return int
     */
    public function countItems();

    public function filter();
}
