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

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformClient implements ApiClientInterface
{
    private ?RequestInterface $request = null;

    public function __construct(
        private AbstractBrowser $client,
        private SharedStorageInterface $sharedStorage,
        private RequestFactoryInterface $requestFactory,
        private string $authorizationHeader,
        private ?string $section = null,
    ) {
    }

    public function index(string $resource): Response
    {
        $this->request = $this->requestFactory->index($this->section, $resource, $this->authorizationHeader, $this->getToken());

        return $this->request($this->request);
    }

    public function showByIri(string $iri): Response
    {
        $request = $this->requestFactory->custom($iri, HttpRequest::METHOD_GET);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function subResourceIndex(string $resource, string $subResource, string $id): Response
    {
        $request = $this->requestFactory->subResourceIndex($this->section, $resource, $id, $subResource);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function show(string $resource, string $id): Response
    {
        return $this->request(
            $this->requestFactory->show(
                $this->section,
                $resource,
                $id,
                $this->authorizationHeader,
                $this->getToken(),
            ),
        );
    }

    public function create(?RequestInterface $request = null): Response
    {
        return $this->request($request ?? $this->request);
    }

    public function update(): Response
    {
        return $this->request($this->request);
    }

    public function resend(): Response
    {
        return $this->request($this->request);
    }

    public function delete(string $resource, string $id): Response
    {
        return $this->request(
            $this->requestFactory->delete(
                $this->section,
                $resource,
                $id,
                $this->authorizationHeader,
                $this->getToken(),
            ),
        );
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

    public function applyTransition(string $resource, string $id, string $transition, array $content = []): Response
    {
        $request = $this->requestFactory->transition($this->section, $resource, $id, $transition);
        $request->authorize($this->getToken(), $this->authorizationHeader);
        $request->setContent($content);

        return $this->request($request);
    }

    public function customItemAction(string $resource, string $id, string $type, string $action): Response
    {
        $request = $this->requestFactory->customItemAction($this->section, $resource, $id, $type, $action);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function customAction(string $url, string $method): Response
    {
        $request = $this->requestFactory->custom($url, $method);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function executeCustomRequest(RequestInterface $request): Response
    {
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request);
    }

    public function buildCreateRequest(string $resource): void
    {
        $this->request = $this->requestFactory->create($this->section, $resource, $this->authorizationHeader, $this->getToken());
    }

    public function buildUpdateRequest(string $resource, string $id): void
    {
        $this->show($resource, $id);

        $this->request = $this->requestFactory->update(
            $this->section,
            $resource,
            $id,
            $this->authorizationHeader,
            $this->getToken(),
        );
        $this->request->setContent(json_decode($this->client->getResponse()->getContent(), true));
    }

    public function buildCustomUpdateRequest(string $resource, string $id, string $customSuffix): void
    {
        $this->request = $this->requestFactory->update(
            $this->section,
            $resource,
            sprintf('%s/%s', $id, $customSuffix),
            $this->authorizationHeader,
            $this->getToken(),
        );
    }

    /** @param string|int $value */
    public function addParameter(string $key, $value): void
    {
        $this->request->updateParameters([$key => $value]);
    }

    public function setRequestData(array $content): void
    {
        $this->request->setContent($content);
    }

    /** @param string|int $value */
    public function addFilter(string $key, $value): void
    {
        $this->addParameter($key, $value);
    }

    public function clearParameters(): void
    {
        $this->request->clearParameters();
    }

    public function addFile(string $key, UploadedFile $file): void
    {
        $this->request->updateFiles([$key => $file]);
    }

    /** @param string|int|bool|array $value */
    public function addRequestData(string $key, $value): void
    {
        $this->request->updateContent([$key => $value]);
    }

    public function updateRequestData(array $data): void
    {
        $this->request->updateContent($data);
    }

    public function setSubResourceData(string $key, array $data): void
    {
        $this->request->setSubResource($key, $data);
    }

    public function addSubResourceData(string $key, array $data): void
    {
        $this->request->addSubResource($key, $data);
    }

    public function removeSubResource(string $subResource, string $id): void
    {
        $this->request->removeSubResource($subResource, $id);
    }

    public function getContent(): array
    {
        return $this->request->getContent();
    }

    public function getLastResponse(): Response
    {
        return $this->client->getResponse();
    }

    public function getToken(): ?string
    {
        return $this->sharedStorage->has('token') ? $this->sharedStorage->get('token') : null;
    }

    public function request(RequestInterface $request): Response
    {
        $this->setServerParameters();

        $this->client->request(
            $request->method(),
            $request->url(),
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content() ?? null,
        );

        return $this->getLastResponse();
    }

    private function setServerParameters(): void
    {
        if ($this->sharedStorage->has('hostname')) {
            $this->client->setServerParameter('HTTP_HOST', $this->sharedStorage->get('hostname'));
        }

        if ($this->sharedStorage->has('current_locale_code')) {
            $this->client->setServerParameter('HTTP_ACCEPT_LANGUAGE', $this->sharedStorage->get('current_locale_code'));
        }
    }
}
