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
    public function isSingleResourceOnPage(array $parameters): bool;

    public function isSingleResourceWithSpecificElementOnPage(array $parameters, string $element): bool;

    public function getColumnFields(string $columnName): array;

    public function sortBy(string $fieldName);

    public function deleteResourceOnPage(array $parameters): void;

    public function getActionsForResource(array $parameters): NodeElement;

    public function checkResourceOnPage(array $parameters): void;

    public function countItems(): int;

    public function filter();

    public function bulkDelete(): void;
}
