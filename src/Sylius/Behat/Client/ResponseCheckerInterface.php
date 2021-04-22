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

use Symfony\Component\HttpFoundation\Response;

interface ResponseCheckerInterface
{
    public function countCollectionItems(Response $response): int;

    public function countTotalCollectionItems(Response $response): int;

    public function getCollection(Response $response): array;

    public function getCollectionItemsWithValue(Response $response, string $key, string $value): array;

    public function getValue(Response $response, string $key);

    public function getTranslationValue(Response $response, string $key, ?string $localeCode): string;

    public function getError(Response $response): string;

    public function isCreationSuccessful(Response $response): bool;

    public function isUpdateSuccessful(Response $response): bool;

    public function isShowSuccessful(Response $response): bool;

    public function isDeletionSuccessful(Response $response): bool;

    public function hasAccessDenied(Response $response): bool;

    public function hasCollection(Response $response): bool;

    /** @param string|int $value */
    public function hasValue(Response $response, string $key, $value): bool;

    /** @param string|int $value */
    public function hasValueInCollection(Response $response, string $key, $value): bool;

    /** @param string|int $value */
    public function hasItemWithValue(Response $response, string $key, $value): bool;

    /** @param string|int $value */
    public function hasSubResourceWithValue(Response $response, string $subResource, string $key, $value): bool;

    /** @param string|array $value */
    public function hasItemOnPositionWithValue(Response $response, int $position, string $key, $value): bool;

    public function hasItemWithTranslation(Response $response, string $locale, string $key, string $translation): bool;

    public function hasKey(Response $response, string $key): bool;

    public function hasTranslation(Response $response, string $locale, string $key, string $translation): bool;

    public function hasItemWithValues(Response $response, array $parameters): bool;

    public function getResponseContent(Response $response): array;

    public function hasViolationWithMessage(Response $response, string $message, ?string $property = null): bool;
}
