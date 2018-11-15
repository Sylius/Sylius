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

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @return bool
     */
    public function isSingleResourceOnPage(array $parameters);

    /**
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
     * @return bool
     */
    public function deleteResourceOnPage(array $parameters);

    /**
     * @return NodeElement
     */
    public function getActionsForResource(array $parameters);

    public function checkResourceOnPage(array $parameters): void;

    /**
     * @return int
     */
    public function countItems();

    public function filter();

    public function bulkDelete(): void;
}
