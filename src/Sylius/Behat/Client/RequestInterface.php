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

interface RequestInterface
{
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
