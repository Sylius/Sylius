<?php

declare(strict_types=1);

namespace Sylius\Behat\Client;

interface ApiClientInterface
{
    public function index(string $resource): void;

    public function buildCreateRequest(string $resource): void;

    public function addRequestData(string $key, string $value): void;

    public function create(): void;

    public function countCollectionItems(): int;

    public function getCollection(): array;

    public function getCurrentPage(): ?string;

    public function getError(): string;

    public function isCreationSuccessful(): bool;
}
