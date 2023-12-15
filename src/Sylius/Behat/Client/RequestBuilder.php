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

final class RequestBuilder
{
    private array $parameters = [];

    private array $headers = [];

    private array $content = [];

    private array $files = [];

    private function __construct(
        private string $uri,
        private string $method,
    ) {
    }

    public static function create(string $uri, string $method): self
    {
        return new self($uri, $method);
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

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
