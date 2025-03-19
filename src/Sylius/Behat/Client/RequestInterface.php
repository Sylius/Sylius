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

    /** @return array<string, mixed> */
    public function headers(): array;

    public function content(): string;

    /** @return array<string, mixed> */
    public function getContent(): array;

    /** @param array<string, mixed> $content */
    public function setContent(array $content): void;

    /** @param array<string, mixed> $newValues */
    public function updateContent(array $newValues): void;

    /** @return array<string, mixed> */
    public function parameters(): array;

    /** @param array<string, mixed> $newParameters */
    public function updateParameters(array $newParameters): void;

    public function clearParameters(): void;

    /** @return array<string, mixed> */
    public function files(): array;

    /** @param array<string, mixed> $newFiles */
    public function updateFiles(array $newFiles): void;

    /** @param array<string, mixed> $subResource */
    public function setSubResource(string $key, array $subResource): void;

    /** @param array<string, mixed> $subResource */
    public function addSubResource(string $key, array $subResource): void;

    public function removeSubResource(string $subResourceKey, string $value, string $key = '@id'): void;

    public function authorize(?string $token, string $authorizationHeader): self;
}
