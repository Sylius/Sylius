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

namespace Sylius\Behat\Client;

interface ApiClientInterface
{
    public function index(string $resource): void;

    public function show(string $resource, string $id): void;

    public function showRelated(string $resource): void;

    public function showByIri(string $iri): void;

    public function subResourceIndex(string $resource, string $subResource, string $id): void;

    public function buildCreateRequest(string $resource): void;

    public function buildUpdateRequest(string $resource, string $id): void;

    /** @param string|int $value */
    public function addRequestData(string $key, $value): void;

    public function addCompoundRequestData(array $data): void;

    public function updateRequestData(array $data): void;

    public function create(): void;

    public function update(): void;

    public function delete(string $resource, string $id): void;

    public function applyTransition(string $resource, string $id, string $transition): void;

    public function countCollectionItems(): int;

    public function getCollectionItems(): array;

    public function getCollectionItemsWithValue(string $key, string $value): array;

    public function getError(): string;

    public function isCreationSuccessful(): bool;

    public function isDeletionSuccessful(): bool;

    public function isUpdateSuccessful(): bool;

    /** @param string|int $value */
    public function responseHasValue(string $key, $value): bool;

    public function relatedResourceHasValue(string $resource, string $key, $value): bool;

    /** @param string|float $value */
    public function hasItemWithValue(string $key, $value): bool;

    public function hasItemOnPositionWithValue(int $position, string $key, string $value): bool;

    public function hasItemWithTranslation(string $locale, string $key, string $translation): bool;
}
