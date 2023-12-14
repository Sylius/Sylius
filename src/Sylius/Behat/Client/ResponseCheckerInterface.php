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

namespace Sylius\Behat\Client;

use Symfony\Component\HttpFoundation\Response;

interface ResponseCheckerInterface
{
    public function countCollectionItems(Response $response): int;

    public function countTotalCollectionItems(Response $response): int;

    public function getCollection(Response $response): array;

    public function getCollectionItemsWithValue(Response $response, string $key, string $value): array;

    public function getValue(Response $response, string $key);

    public function getTranslationValue(Response $response, string $key, ?string $localeCode): string;

    public function getError(Response $response): ?string;

    public function isAccepted(Response $response): bool;

    public function isCreationSuccessful(Response $response): bool;

    public function isUpdateSuccessful(Response $response): bool;

    public function isShowSuccessful(Response $response): bool;

    public function isDeletionSuccessful(Response $response): bool;

    public function hasAccessDenied(Response $response): bool;

    public function hasCollection(Response $response): bool;

    public function hasValue(Response $response, string $key, int|string $value): bool;

    public function hasValueInCollection(Response $response, string $key, int|string $value): bool;

    public function hasItemWithValue(Response $response, string $key, int|string $value): bool;

    /** @param array<string, int|string> $expectedValues */
    public function hasValuesInAnySubresourceObjectCollection(
        Response $response,
        string $subResource,
        array $expectedValues,
    ): bool;

    /** @param array<string, int|string> $expectedValues */
    public function hasValuesInSubresourceObject(
        Response $response,
        string $subResource,
        array $expectedValues,
    ): bool;

    public function hasItemOnPositionWithValue(Response $response, int $position, string $key, array|string $value): bool;

    public function hasItemWithTranslation(Response $response, string $locale, string $key, string $translation): bool;

    public function hasKey(Response $response, string $key): bool;

    public function hasTranslation(Response $response, string $locale, string $key, string $translation): bool;

    public function hasItemWithValues(Response $response, array $parameters): bool;

    public function getResponseContent(Response $response): array;

    public function hasViolationWithMessage(Response $response, string $message, ?string $property = null): bool;
}
