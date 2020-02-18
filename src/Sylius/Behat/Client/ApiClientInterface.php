<?php

declare(strict_types=1);

namespace Sylius\Behat\Client;

interface ApiClientInterface
{
    public function index(string $resource): void;

    public function countCollectionItems(): int;

    public function getCollection(): array;
}
