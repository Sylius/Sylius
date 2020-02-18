<?php

declare(strict_types=1);

namespace Sylius\Behat\Client;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var array */
    private $request;

    /** @var Response|null */
    private $response = null;

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    public function index(string $resource): void
    {
        $this->response = null;
        $this->client->request('GET', '/new-api/'.$resource, [], [], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function buildCreateRequest(string $resource): void
    {
        $this->request = ['url' => '/new-api/'.$resource];
    }

    public function addRequestData(string $key, string $value): void
    {
        $this->request['body'][$key] = $value;
    }

    public function create(): void
    {
        $content = json_encode($this->request['body']);

        $this->response = null;
        $this->client->request(
            'POST', $this->request['url'], [], [], ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/ld+json'], $content
        );
    }

    public function countCollectionItems(): int
    {
        return count($this->getResponseContent()['hydra:member']);
    }

    public function getCollection(): array
    {
        return $this->getResponseContent()['hydra:member'];
    }

    public function getCurrentPage(): ?string
    {
        if ($this->getResponseContent()['@type'] === 'hydra:Collection') {
            return 'index';
        }

        return null;
    }

    public function isCreationSuccessful(): bool
    {
        return $this->getResponse()->getStatusCode() === Response::HTTP_CREATED;
    }

    private function getResponseContent(): array
    {
        return json_decode($this->getResponse()->getContent(), true);
    }

    private function getResponse(): Response
    {
        if ($this->response === null) {
            $this->response = $this->client->getResponse();
        }

        return $this->response;
    }

}
