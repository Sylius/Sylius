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

interface RequestBuilderInterface
{
    public static function createGet(string $uri): self;

    public static function createPost(string $uri): self;

    public static function createPut(string $uri): self;

    public static function createDelete(string $uri): self;

    /** @param array<string, mixed> $content */
    public function withContent(array $content): self;

    public function withHeader(string $key, string $value): self;

    public function withFile(string $key, UploadedFile $file): self;

    /** @param array<string, mixed> $value */
    public function withParameter(string $key, array|string $value): self;

    public function build(): RequestInterface;
}
