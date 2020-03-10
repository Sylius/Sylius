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

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string */
    private $resource;

    /** @var Request */
    private $request;

    /** @var array */
    private $filters;

    public function __construct(AbstractBrowser $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    public function setResource(string $resource): void
    {
        $this->resource = $resource;
    }

    public function index(): void
    {
        $this->request(Request::index($this->resource, $this->sharedStorage->get('token')));
    }

    public function showRelated(string $resource): void
    {
        $this->request('GET', $this->getResponseContentValue($resource), ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function showByIri(string $iri): void
    {
        $this->request('GET', $iri, ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function subResourceIndex(string $subResource, string $id): void
    {
        $this->request(Request::subResourceIndex($this->resource, $id, $subResource, $this->sharedStorage->get('token')));
    }

    public function show(string $id): void
    {
        $this->request(Request::show($this->resource, $id, $this->sharedStorage->get('token')));
    }

    public function create(): void
    {
        $this->request($this->request);
    }

    public function update(): void
    {
        $this->request($this->request);
    }

    public function buildFilter(array $filters): void
    {
        $this->filters = $filters;
    }

    public function delete(string $id): void
    {
        $this->request(Request::delete($this->resource, $id, $this->sharedStorage->get('token')));
    }

    public function applyTransition(string $id, string $transition): void
    {
        $this->request(Request::transition($this->resource, $id, $transition, $this->sharedStorage->get('token')));
    }

    public function buildCreateRequest(): void
    {
        $this->request = Request::create($this->resource, $this->sharedStorage->get('token'));
    }

    public function buildUpdateRequest(string $id): void
    {
        $this->show($id);

        $this->request = Request::update($this->resource, $id, $this->sharedStorage->get('token'));
        $this->request->setContent(json_decode($this->client->getResponse()->getContent(), true));
    }

    /** @param string|int $value */
    public function addRequestData(string $key, $value): void
    {
        $this->request->updateContent([$key => $value]);
    }

    public function updateRequestData(array $data): void
    {
        $this->request->updateContent($data);
    }

    public function filter(string $resource): void
    {
        $query = http_build_query($this->filters, '', '&', PHP_QUERY_RFC3986);
        $path = sprintf('/new-api/%s?%s', $resource, $query);

        $this->request('GET', $path, ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function addSubResourceData(string $key, array $data): void
    {
        $this->request->addSubResource($key, $data);
    }

    public function countCollectionItems(): int
    {
        return (int) $this->getResponseContentValue('hydra:totalItems');
    }

    public function getCollectionItems(): array
    {
        return $this->getResponseContentValue('hydra:member');
    }

    public function getCollectionItemsWithValue(string $key, string $value): array
    {
        $items = array_filter($this->getCollectionItems(), function (array $item) use ($key, $value): bool {
            return $item[$key] === $value;
        });

        return $items;
    }

    public function getError(): string
    {
        return $this->getResponseContentValue('hydra:description');
    }

    public function isCreationSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_CREATED;
    }

    public function isUpdateSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_OK;
    }

    public function isDeletionSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    /** @param string|int $value */
    public function responseHasValue(string $key, $value): bool
    {
        return $this->getResponseContentValue($key) === $value;
    }

    /** @param string|int $value */
    public function relatedResourceHasValue(string $resource, string $key, $value): bool
    {
        $this->showRelated($resource);

        return $this->getResponseContentValue($key) === $value;
    }

    /** @param string|float $value */
    public function hasItemWithValue(string $key, $value): bool
    {
        foreach ($this->getCollectionItems() as $resource) {
            if ($resource[$key] === $value) {
                return true;
            }
        }

        return false;
    }

    public function hasItemOnPositionWithValue(int $position, string $key, string $value): bool
    {
        return $this->getCollectionItems()[$position][$key] === $value;
    }

    public function hasItemWithTranslation(string $locale, string $key, string $translation): bool
    {
        foreach ($this->getCollectionItems() as $resource) {
            if (
                isset($resource['translations']) &&
                isset($resource['translations'][$locale]) &&
                $resource['translations'][$locale][$key] === $translation
            ) {
                return true;
            }
        }

        return false;
    }

    private function request(Request $request): void
    {
        $this->client->request($request->method(), $request->url(), [], [], $request->headers(), $request->content() ?? null);
    }

    private function getResponseContentValue(string $key)
    {
        $content = json_decode($this->client->getResponse()->getContent(), true);

        Assert::keyExists($content, $key);

        return $content[$key];
    }
}
