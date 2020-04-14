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
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function __construct(AbstractBrowser $client, SharedStorageInterface $sharedStorage, string $resource)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->resource = $resource;
    }

    public function index(): Response
    {
        $this->request = Request::index($this->resource, $this->sharedStorage->get('token'));

        return $this->request($this->request);
    }

    public function showByIri(string $iri): Response
    {
        return $this->request(Request::custom($iri, 'GET', $this->sharedStorage->get('token')));
    }

    public function subResourceIndex(string $subResource, string $id): Response
    {
        return $this->request(Request::subResourceIndex($this->resource, $id, $subResource, $this->sharedStorage->get('token')));
    }

    public function show(string $id): Response
    {
        return $this->request(Request::show($this->resource, $id, $this->sharedStorage->get('token')));
    }

    public function create(): Response
    {
        return $this->request($this->request);
    }

    public function update(): Response
    {
        return $this->request($this->request);
    }

    public function delete(string $id): Response
    {
        return $this->request(Request::delete($this->resource, $id, $this->sharedStorage->get('token')));
    }

    public function filter(): Response
    {
        return $this->request($this->request);
    }

    public function sort(array $sorting): Response
    {
        $this->request->updateParameters(['order' => $sorting]);

        return $this->request($this->request);
    }

    public function applyTransition(string $id, string $transition, array $content = []): Response
    {
        $request = Request::transition($this->resource, $id, $transition, $this->sharedStorage->get('token'));
        $request->setContent($content);

        return $this->request($request);
    }

    public function customItemAction(string $id, string $type, string $action): Response
    {
        $request = Request::customItemAction($this->resource, $id, $type, $action, $this->sharedStorage->get('token'));

        return $this->request($request);
    }

    public function customAction(string $url, string $method): Response
    {
        $token = $this->sharedStorage->has('token') ? $this->sharedStorage->get('token') : null;
        $request = Request::custom($url, $method, $token);

        return $this->request($request);
    }

    public function upload(): Response
    {
        return $this->request($this->request);
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

    public function buildUploadRequest(): void
    {
        $this->request = Request::upload($this->resource, $this->sharedStorage->get('token'));
    }

    /** @param string|int $value */
    public function addParameter(string $key, $value): void
    {
        $this->request->updateParameters([$key => $value]);
    }

    /** @param string|int $value */
    public function addFilter(string $key, $value): void
    {
        $this->addParameter($key, $value);
    }

    public function addFile(string $key, UploadedFile $file): void
    {
        $this->request->updateFiles([$key => $file]);
    }

    /** @param string|int|array $value */
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

    public function getLastResponse(): Response
    {
        return $this->client->getResponse();
    }

    private function request(RequestInterface $request): Response
    {
        $this->client->request(
            $request->method(),
            $request->url(),
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content() ?? null
        );

        return $this->getLastResponse();
    }
}
