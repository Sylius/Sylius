<?php

declare(strict_types=1);

namespace Sylius\Behat\Client;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var Response|null */
    private $response = null;

    public function __construct(AbstractBrowser $client)
    {
        $this->client = $client;
    }

    public function index(string $resource): void
    {
        $this->client->request('GET', '/new-api/'.$resource, [], [], ['HTTP_ACCEPT' => 'application/ld+json']);
    }

    public function countCollectionItems(): int
    {
        return count($this->getResponseContent()['hydra:member']);
    }

    public function getCollection(): array
    {
        return $this->getResponseContent()['hydra:member'];
    }

    private function getResponseContent(): array
    {
        if ($this->response === null) {
            $this->response = $this->client->getResponse();
        }

        return json_decode($this->response->getContent(), true);
    }
}
