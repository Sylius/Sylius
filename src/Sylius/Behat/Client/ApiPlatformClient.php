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

final class ApiPlatformClient implements ApiClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string */
    private $resource;

    /** @var RequestInterface */
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

    public function showByIri(string $iri): void
    {
        $this->request(Request::custom($iri, 'GET', $this->sharedStorage->get('token')));
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

    public function delete(string $id): void
    {
        $this->request(Request::delete($this->resource, $id, $this->sharedStorage->get('token')));
    }

    public function filter(): void
    {
        $query = http_build_query($this->filters, '', '&', PHP_QUERY_RFC3986);
        $path = sprintf('/new-api/%s?%s', $this->resource, $query);

        $this->request(Request::custom($path, 'GET', $this->sharedStorage->get('token')));
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

    public function buildFilter(array $filters): void
    {
        $this->filters = $filters;
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

    public function addSubResourceData(string $key, array $data): void
    {
        $this->request->addSubResource($key, $data);
    }

    public function getResponse(): Response
    {
        return $this->client->getResponse();
    }

    private function request(RequestInterface $request): void
    {
        $this->client->request($request->method(), $request->url(), [], [], $request->headers(), $request->content() ?? null);
    }
}
