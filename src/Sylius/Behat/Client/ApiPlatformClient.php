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

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var array */
    private $request = ['url' => null, 'body' => []];

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    public function index(string $resource): void
    {
        $this->client->request('GET', '/new-api/'.$resource, [], [], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function subResourceIndex(string $resource, string $subResource, string $id): void
    {
        $url = sprintf('/new-api/%s/%s/%s', $resource, $id, $subResource);

        $this->client->request('GET', $url, [], [], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function buildCreateRequest(string $resource): void
    {
        $this->request['url'] = '/new-api/'.$resource;
    }

    public function addRequestData(string $key, string $value): void
    {
        $this->request['body'][$key] = $value;
    }

    public function addCompoundRequestData(array $data): void
    {
        $this->request['body'] = array_merge_recursive($this->request['body'], $data);
    }

    public function create(): void
    {
        $content = json_encode($this->request['body']);

        $this->request('POST', $this->request['url'], ['CONTENT_TYPE' => 'application/json'], $content);
    }

    public function countCollectionItems(): int
    {
        return (int) $this->getResponseContentValue('hydra:totalItems');
    }

    public function delete(string $resource, string $id): void
    {
        $this->request('DELETE', sprintf('/new-api/%s/%s', $resource, $id ), []);
    }

    public function getCollection(): array
    {
        return $this->getResponseContentValue('hydra:member');
    }

    public function getCollectionItemsWithValue(string $key, string $value): array
    {
        $items = [];
        foreach ($this->getCollection() as $item) {
            if ($item[$key] === $value) $items[] = $item;
        }

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

    public function isDeletionSuccessful(): bool
    {
        return $this->client->getResponse()->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function hasItemWithValue(string $key, string $value): bool
    {
        foreach ($this->getCollection() as $resource) {
            if ($resource[$key] === $value) {
                return true;
            }
        }

        return false;
    }

    private function request(string $method, string $url, array $headers, string $content = null): void
    {
        $this->client->request(
            $method, $url, [], [], array_merge(['HTTP_ACCEPT' => 'application/ld+json'], $headers), $content
        );
    }

    private function getResponseContentValue(string $key)
    {
        $content = json_decode($this->client->getResponse()->getContent(), true);

        Assert::keyExists($content, $key);

        return $content[$key];
    }
}
