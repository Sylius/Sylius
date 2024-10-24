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

    private ?Response $lastResponse = null;

    public function __construct(
        private readonly AbstractBrowser $client,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly string $authorizationHeader,
        private readonly string $section,
    ) {
    }

    public function index(string $resource, array $queryParameters = [], bool $forgetResponse = false): Response
    {
        $this->request = $this
            ->requestFactory
            ->index($this->section, $resource, $this->authorizationHeader, $this->getToken(), $queryParameters)
        ;

        return $this->request($this->request, $forgetResponse);
    }

    public function showByIri(string $iri, bool $forgetResponse = false): Response
    {
        $request = $this->requestFactory->custom($iri, HttpRequest::METHOD_GET);
        $request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($request, $forgetResponse);
    }

    /** @param array<string, string> $queryParameters */
    public function subResourceIndex(string $resource, string $subResource, string $id, array $queryParameters = [], bool $forgetResponse = false): Response
    {
        $this->request = $this->requestFactory->subResourceIndex($this->section, $resource, $id, $subResource, $queryParameters);
        $this->request->authorize($this->getToken(), $this->authorizationHeader);

        return $this->request($this->request, $forgetResponse);
    }

    public function show(string $resource, string $id, bool $forgetResponse = false): Response
    {
        return $this->request(
            $this->requestFactory->show(
                $this->section,
                $resource,
                $id,
                $this->authorizationHeader,
                $this->getToken(),
            ),
            $forgetResponse,
        );
    }

    public function create(?RequestInterface $request = null, bool $forgetResponse = false): Response
    {
        return $this->request($request ?? $this->request, $forgetResponse);
    }

    public function update(bool $forgetResponse = false): Response
    {
        return $this->request($this->request, $forgetResponse);
    }

    public function resend(bool $forgetResponse = false): Response
    {
        return $this->request($this->request, $forgetResponse);
    }

    public function delete(string $resource, string $id, bool $forgetResponse = false): Response
    {
        return $this->request(
            $this->requestFactory->delete(
                $this->section,
                $resource,
                $id,
                $this->authorizationHeader,
                $this->getToken(),
            ),
            $forgetResponse,
        );
    }

    public function filter(): Response
    {
        return $this->request($this->request);
    }

    public function sort(array $sorting): Response
    {
        if ($this->request === null) {
            throw new \RuntimeException('There is no request to sort.');
        }

        $this->request->updateParameters(['order' => $sorting]);

        return $this->request($this->request);
    }

    /** @param array<string, mixed> $content */
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

    public function buildCreateRequest(string $url): self
    {
        $this->validateUri($url);
        $this->request = $this->requestFactory->default(
            section: $this->section,
            url: $url,
            method: 'POST',
            token: $this->getToken(),
        );

        return $this;
    }

    public function buildUpdateRequest(string $uri, ?string $id = null): self
    {
        $this->validateUri($uri);

        if ($id !== null) {
            $uri = sprintf('%s/%s', $uri, $id);
        }

        $response = $this->requestGet($uri);

        $this->request = $this->requestFactory->update(
            $this->section,
            $uri,
            $this->authorizationHeader,
            $this->getToken(),
        );

        $this->request->setContent(json_decode($response->getContent(), true));

        return $this;
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

    public function addParameter(string $key, bool|int|string $value): self
    {
        $this->request->updateParameters([$key => $value]);

        return $this;
    }

    public function setRequestData(array $data): self
    {
        $this->request->setContent($data);

        return $this;
    }

    public function addFilter(string $key, bool|int|string $value): void
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

    /** @param array<string, mixed> $value */
    public function addRequestData(string $key, array|bool|int|string|null $value): self
    {
        $this->request->updateContent([$key => $value]);

        return $this;
    }

    /** @param array<string, mixed> $value */
    public function replaceRequestData(string $key, array|bool|int|string|null $value): void
    {
        $requestContent = $this->request->getContent();

        $this->request->setContent(array_replace($requestContent, [$key => $value]));
    }

    /** @param array<string, mixed> $data */
    public function updateRequestData(array $data): void
    {
        $this->request->updateContent($data);
    }

    /** @param array<string, mixed> $data */
    public function setSubResourceData(string $key, array $data): void
    {
        $this->request->setSubResource($key, $data);
    }

    /** @param array<string, mixed> $data */
    public function addSubResourceData(string $key, array $data): void
    {
        $this->request->addSubResource($key, $data);
    }

    public function removeSubResourceIri(string $subResourceKey, string $iri): void
    {
        $this->request->removeSubResource($subResourceKey, $iri);
    }

    public function removeSubResourceObject(string $subResourceKey, string $value, string $key = '@id'): void
    {
        $this->request->removeSubResource($subResourceKey, $value, $key);
    }

    /** @return array<string, mixed> */
    public function getContent(): array
    {
        return $this->request->getContent();
    }

    public function getLastResponse(): Response
    {
        if (null === $this->lastResponse) {
            throw new \RuntimeException('There is no last response.');
        }

        return $this->lastResponse;
    }

    public function getToken(): ?string
    {
        return $this->sharedStorage->has('token') ? $this->sharedStorage->get('token') : null;
    }

    public function requestGet(string $uri, array $queryParameters = [], array $headers = []): Response
    {
        $this->validateUri($uri);

        $this->request = $this
            ->requestFactory
            ->default($this->section, $uri, HttpRequest::METHOD_GET, $queryParameters, $headers)
            ->authorize($this->getToken(), $this->authorizationHeader)
        ;

        return $this->request();
    }

    public function requestPatch(string $uri, array $body = [], array $queryParameters = [], array $headers = []): Response
    {
        $this->validateUri($uri);

        $this->request = $this
            ->requestFactory
            ->default($this->section, $uri, HttpRequest::METHOD_PATCH, $queryParameters, $headers)
            ->authorize($this->getToken(), $this->authorizationHeader)
        ;

        $this->request->setContent($body);

        return $this->request();
    }

    public function requestDelete(string $uri): Response
    {
        $this->request = $this
            ->requestFactory
            ->default($this->section, $uri, HttpRequest::METHOD_DELETE)
            ->authorize($this->getToken(), $this->authorizationHeader)
        ;

        return $this->request();
    }

    public function request(?RequestInterface $request = null, bool $forgetResponse = false): Response
    {
        if ($request === null) {
            $request = $this->request;
        }

        $this->setServerParameters();

        $this->client->request(
            $request->method(),
            $request->url(),
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content(),
        );

        /** @var Response $response */
        $response = $this->client->getResponse();

        if (false === $forgetResponse) {
            $this->lastResponse = $response;
        }

        return $response;
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

    private function validateUri(string $uri): void
    {
        if (str_starts_with($uri, '/')) {
            throw new \InvalidArgumentException('URI should not start with a slash.');
        }

        if (str_starts_with($uri, 'http')) {
            throw new \InvalidArgumentException('URI should not start with "http".');
        }

        if (str_starts_with($uri, 'api')) {
            throw new \InvalidArgumentException('URI should not start with "api".');
        }
    }
}
