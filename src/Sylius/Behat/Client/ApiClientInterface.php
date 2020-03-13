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

use Symfony\Component\HttpFoundation\Response;

interface ApiClientInterface
{
    public function index(): void;

    public function showByIri(string $iri): void;

    public function subResourceIndex(string $subResource, string $id): void;

    public function show(string $id): void;

    public function create(): void;

    public function update(): void;

    public function delete(string $id): void;

    public function filter(): void;

    public function applyTransition(string $id, string $transition): void;

    public function buildCreateRequest(): void;

    public function buildUpdateRequest(string $id): void;

    public function buildFilter(array $filters): void;

    /** @param string|int $value */
    public function addRequestData(string $key, $value): void;

    public function addSubResourceData(string $key, array $data): void;

    public function updateRequestData(array $data): void;

    public function getResponse(): Response;
}
