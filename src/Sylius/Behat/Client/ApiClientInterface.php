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

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

interface ApiClientInterface
{
    public function request(?RequestInterface $request = null, bool $forgetResponse = false): Response;

    /** @param array<string, mixed> $queryParameters */
    public function index(string $resource, array $queryParameters = [], bool $forgetResponse = false): Response;

    public function showByIri(string $iri, bool $forgetResponse = false): Response;

    /** @param array<string, string> $queryParameters */
    public function subResourceIndex(string $resource, string $subResource, string $id, array $queryParameters = [], bool $forgetResponse = false): Response;

    public function show(string $resource, string $id, bool $forgetResponse = false): Response;

    public function create(?RequestInterface $request = null, bool $forgetResponse = false): Response;

    public function update(bool $forgetResponse = false): Response;

    public function delete(string $resource, string $id, bool $forgetResponse = false): Response;

    public function filter(): Response;

    /** @param array<string, mixed> $sorting */
    public function sort(array $sorting): Response;

    /** @param array<string, mixed> $content */
    public function applyTransition(string $resource, string $id, string $transition, array $content = []): Response;

    public function customItemAction(string $resource, string $id, string $type, string $action): Response;

    public function customAction(string $url, string $method): Response;

    public function resend(): Response;

    public function executeCustomRequest(RequestInterface $request): Response;

    public function buildCreateRequest(string $url): self;

    /** @param ?string $id Deprecated, pass the id as a part of the uri */
    public function buildUpdateRequest(string $uri, ?string $id = null): self;

    public function buildCustomUpdateRequest(string $uri, ?string $id = null): self;

    /** @param array<string, mixed> $data */
    public function setRequestData(array $data): self;

    public function addParameter(string $key, bool|int|string $value): self;

    public function addFilter(string $key, bool|int|string $value): void;

    public function clearParameters(): void;

    public function addFile(string $key, UploadedFile $file): void;

    /** @param array<string, mixed> $value */
    public function addRequestData(string $key, array|bool|int|string|null $value): self;

    /** @param array<string, mixed> $value */
    public function replaceRequestData(string $key, array|bool|int|string|null $value): void;

    /** @param array<string, mixed> $data */
    public function setSubResourceData(string $key, array $data): void;

    /** @param array<string, mixed> $data */
    public function addSubResourceData(string $key, array $data): void;

    public function removeSubResourceIri(string $subResourceKey, string $iri): void;

    public function removeSubResourceObject(string $subResourceKey, string $value, string $key = '@id'): void;

    /** @param array<string, mixed> $data */
    public function updateRequestData(array $data): void;

    /** @return array<string, mixed> */
    public function getContent(): array;

    public function getLastResponse(): Response;

    public function getToken(): ?string;

    /**
     * @param array<string, int|string|bool> $queryParameters
     * @param array<string, string> $headers
     */
    public function requestGet(string $uri, array $queryParameters = [], array $headers = []): Response;

    /**
     * @param array<string, int|string|bool> $body
     * @param array<string, int|string|bool> $queryParameters
     * @param array<string, string> $headers
     */
    public function requestPatch(string $uri, array $body = [], array $queryParameters = [], array $headers = []): Response;

    public function requestDelete(string $uri): Response;
}
