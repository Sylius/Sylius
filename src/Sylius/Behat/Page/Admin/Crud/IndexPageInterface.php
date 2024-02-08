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

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface IndexPageInterface extends SymfonyPageInterface
{
    public function isSingleResourceOnPage(array $parameters): bool;

    public function isSingleResourceWithSpecificElementOnPage(array $parameters, string $element): bool;

    public function getColumnFields(string $columnName): array;

    public function sortBy(string $fieldName, ?string $order = null): void;

    public function deleteResourceOnPage(array $parameters): void;

    public function getActionsForResource(array $parameters): NodeElement;

    public function checkResourceOnPage(array $parameters): void;

    public function countItems(): int;

    public function chooseEnabledFilter(): void;

    public function filter(): void;

    public function bulkDelete(): void;

    public function sort(string $order): void;

    public function isEnabledFilterApplied(): bool;
}
