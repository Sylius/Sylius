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
    public static function index(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function subResourceIndex(?string $section, string $resource, string $id, string $subResource): self;

    public static function show(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function create(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function update(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function delete(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function transition(?string $section, string $resource, string $id, string $transition): self;

    public static function customItemAction(?string $section, string $resource, string $id, string $type, string $action): self;

    public static function upload(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): self;

    public static function custom(string $url, string $method, ?string $token = null): self;

    public function url(): string;

    public function method(): string;

    public function headers(): array;

    public function content(): string;

    public function getContent(): array;

    public function setContent(array $content): void;

    public function updateContent(array $newValues): void;

    public function parameters(): array;

    public function updateParameters(array $newParameters): void;

    public function clearParameters(): void;

    public function files(): array;

    public function updateFiles(array $newFiles): void;

    public function setSubResource(string $key, array $subResource): void;

    public function addSubResource(string $key, array $subResource): void;

    public function removeSubResource(string $subResource, string $id): void;

    public function authorize(?string $token, string $authorizationHeader): void;
}
