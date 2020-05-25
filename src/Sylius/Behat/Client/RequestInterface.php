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

interface RequestInterface
{
    public static function index(string $resource, string $token): self;

    public static function subResourceIndex(string $resource, string $id, string $subResource, string $token): self;

    public static function show(string $resource, string $id, string $token): self;

    public static function create(string $resource, string $token): self;

    public static function update(string $resource, string $id, string $token): self;

    public static function delete(string $resource, string $id, string $token): self;

    public static function transition(string $resource, string $id, string $transition, string $token): self;

    public static function upload(string $resource, string $token): self;

    public static function custom(string $url, string $method, ?string $token): self;

    public function url(): string;

    public function method(): string;

    public function headers(): array;

    public function content(): string;

    public function setContent(array $content): void;

    public function updateContent(array $newValues): void;

    public function parameters(): array;

    public function updateParameters(array $newParameters): void;

    public function files(): array;

    public function updateFiles(array $newFiles): void;

    public function addSubResource(string $key, array $subResource): void;

    public function removeSubResource(string $subResource, string $id): void;
}
