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

final class RequestBuilder implements RequestBuilderInterface
{
    /** @var array<string, mixed> */
    private array $parameters = [];

    /** @var array<string, mixed> */
    private array $headers = [];

    /** @var array<string, mixed> */
    private array $content = [];

    /** @var array<string, UploadedFile> */
    private array $files = [];

    private function __construct(
        private readonly string $uri,
        private readonly string $method,
    ) {
    }

    /** @deprecated Use method-specific factory methods instead */
    public static function create(string $uri, string $method): self
    {
        return new self($uri, $method);
    }

    public static function createGet(string $uri): self
    {
        return new self($uri, 'GET');
    }

    public static function createPost(string $uri): RequestBuilderInterface
    {
        return new self($uri, 'POST');
    }

    public static function createPut(string $uri): self
    {
        return new self($uri, 'PUT');
    }

    public static function createDelete(string $uri): self
    {
        return new self($uri, 'DELETE');
    }

    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function withFile(string $key, UploadedFile $file): self
    {
        $this->files[$key] = $file;

        return $this;
    }

    public function withContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function withParameter(string $key, array|string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function build(): RequestInterface
    {
        return new Request(
            $this->uri,
            $this->method,
            $this->parameters,
            $this->headers,
            $this->content,
            $this->files,
        );
    }
}
