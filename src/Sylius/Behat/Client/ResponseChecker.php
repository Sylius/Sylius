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

use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Sylius\Behat\Service\SprintfResponseEscaper;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ResponseChecker implements ResponseCheckerInterface
{
    public function countCollectionItems(Response $response): int
    {
        return count($this->getCollection($response));
    }

    public function countTotalCollectionItems(Response $response): int
    {
        return (int) $this->getResponseContentValue($response, 'hydra:totalItems');
    }

    public function getCollection(Response $response): array
    {
        return $this->getResponseContentValue($response, 'hydra:member');
    }

    public function getCollectionItemsWithValue(Response $response, string $key, string $value): array
    {
        $items = array_filter($this->getCollection($response), fn (array $item): bool => $item[$key] === $value);

        return $items;
    }

    public function getValue(Response $response, string $key)
    {
        return $this->getResponseContentValue($response, $key);
    }

    public function getTranslationValue(Response $response, string $key, ?string $localeCode = 'en_US'): string
    {
        $translations = $this->getResponseContentValue($response, 'translations');

        return $translations[$localeCode][$key];
    }

    public function getError(Response $response): string
    {
        if ($this->hasKey($response, 'message')) {
            return $this->getValue($response, 'message');
        }

        return $this->getResponseContentValue($response, 'hydra:description');
    }

    public function isAccepted(Response $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_ACCEPTED;
    }

    public function isCreationSuccessful(Response $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_CREATED;
    }

    public function isDeletionSuccessful(Response $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function hasAccessDenied(Response $response): bool
    {
        if (!$response instanceof JWTAuthenticationFailureResponse) {
            return false;
        }

        return
            $response->getMessage() === 'JWT Token not found' &&
            $response->getStatusCode() === Response::HTTP_UNAUTHORIZED;
    }

    public function hasCollection(Response $response): bool
    {
        return $this->hasKey($response, 'hydra:member');
    }

    public function isShowSuccessful(Response $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_OK;
    }

    public function isUpdateSuccessful(Response $response): bool
    {
        return $response->getStatusCode() === Response::HTTP_OK;
    }

    /** @param string|int $value */
    public function hasValue(Response $response, string $key, $value): bool
    {
        return $this->getResponseContentValue($response, $key) === $value;
    }

    /** @param string|int $value */
    public function hasValueInCollection(Response $response, string $key, $value): bool
    {
        return in_array($value, $this->getResponseContentValue($response, $key), true);
    }

    /** @param string|int $value */
    public function hasItemWithValue(Response $response, string $key, $value): bool
    {
        foreach ($this->getCollection($response) as $resource) {
            if ($resource[$key] === $value) {
                return true;
            }
        }

        return false;
    }

    /** @param string|int $value */
    public function hasSubResourceWithValue(Response $response, string $subResource, string $key, $value): bool
    {
        foreach ($this->getResponseContentValue($response, $subResource) as $resource) {
            if ($resource[$key] === $value) {
                return true;
            }
        }

        return false;
    }

    /** @param string|array $value */
    public function hasItemOnPositionWithValue(Response $response, int $position, string $key, $value): bool
    {
        return $this->getCollection($response)[$position][$key] === $value;
    }

    public function hasItemWithTranslation(Response $response, string $locale, string $key, string $translation): bool
    {
        if (!$this->hasCollection($response)) {
            $resource = $this->getResponseContent($response);

            if (isset($resource['translations'][$locale]) && $resource['translations'][$locale][$key] === $translation) {
                return true;
            }
        }

        foreach ($this->getCollection($response) as $resource) {
            if (isset($resource['translations'][$locale]) && $resource['translations'][$locale][$key] === $translation) {
                return true;
            }
        }

        return false;
    }

    public function hasKey(Response $response, string $key): bool
    {
        $content = json_decode($response->getContent(), true);

        return array_key_exists($key, $content);
    }

    public function hasTranslation(Response $response, string $locale, string $key, string $translation): bool
    {
        $resource = $this->getResponseContent($response);

        return isset($resource['translations'][$locale]) && $resource['translations'][$locale][$key] === $translation;
    }

    public function hasItemWithValues(Response $response, array $parameters): bool
    {
        foreach ($this->getCollection($response) as $item) {
            if ($this->itemHasValues($item, $parameters)) {
                return true;
            }
        }

        return false;
    }

    public function getResponseContent(Response $response): array
    {
        return json_decode($response->getContent(), true);
    }

    public function hasViolationWithMessage(Response $response, string $message, ?string $property = null): bool
    {
        if (!$this->hasKey($response, 'violations')) {
            return false;
        }

        $violations = $this->getResponseContent($response)['violations'];
        foreach ($violations as $violation) {
            if ($violation['message'] === $message && $property === null) {
                return true;
            }

            if ($violation['message'] === $message && $property !== null && $violation['propertyPath'] === $property) {
                return true;
            }
        }

        return false;
    }

    private function getResponseContentValue(Response $response, string $key)
    {
        $content = json_decode($response->getContent(), true);

        Assert::isArray(
            $content,
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent(
                'Content could not be parsed to array.',
                $response,
            ),
        );

        Assert::keyExists(
            $content,
            $key,
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent(
                'Expected \'' . $key . '\' not found.',
                $response,
            ),
        );

        return $content[$key];
    }

    private function itemHasValues(array $element, array $parameters): bool
    {
        foreach ($parameters as $key => $value) {
            if ($element[$key] !== $value) {
                return false;
            }
        }

        return true;
    }
}
