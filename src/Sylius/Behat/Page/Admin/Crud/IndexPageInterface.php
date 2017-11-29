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
use Sylius\Behat\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function isSingleResourceOnPage(array $parameters): bool;

    /**
     * @param array $parameters
     * @param string $element
     *
     * @return bool
     */
    public function isSingleResourceWithSpecificElementOnPage(array $parameters, string $element): bool;

    /**
     * @param string $columnName
     *
     * @return array
     */
    public function getColumnFields(string $columnName): array;

    /**
     * @param string $fieldName
     */
    public function sortBy(string $fieldName): void;

    /**
     * @param array $parameters
     *
     * @return bool
     */
    public function deleteResourceOnPage(array $parameters): bool;

    /**
     * @param array $parameters
     *
     * @return NodeElement
     */
    public function getActionsForResource(array $parameters): NodeElement;

    /**
     * @param array $parameters
     */
    public function checkResourceOnPage(array $parameters): void;

    /**
     * @return int
     */
    public function countItems(): int;

    public function filter(): void;

    public function bulkDelete(): void;
}
