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

final class Request implements RequestInterface
{
    public function __construct(
        private string $url,
        private string $method,
        private array $parameters = [],
        private array $headers = [],
        private array $content = [],
        private array $files = [],
    ) {
    }

    public function url(): string
    {
        return $this->url;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function content(): string
    {
        return json_encode($this->content);
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    public function updateContent(array $newValues): void
    {
        $this->content = $this->mergeArraysUniquely($this->content, $newValues);
    }

    public function parameters(): array
    {
        return $this->parameters;
    }

    public function updateParameters(array $newParameters): void
    {
        $this->parameters = $this->mergeArraysUniquely($this->parameters, $newParameters);
    }

    public function clearParameters(): void
    {
        $this->parameters = [];
    }

    public function files(): array
    {
        return $this->files;
    }

    public function updateFiles(array $newFiles): void
    {
        $this->files = array_merge($this->files, $newFiles);
    }

    public function setSubresource(string $key, array $subResource): void
    {
        $this->content[$key] = $subResource;
    }

    public function addSubResource(string $key, array $subResource): void
    {
        $this->content[$key][] = $subResource;
    }

    public function removeSubResource(string $subResource, string $id): void
    {
        foreach ($this->content[$subResource] as $key => $resource) {
            if ($resource === $id) {
                unset($this->content[$subResource][$key]);
            }
        }
    }

    public function authorize(?string $token, string $authorizationHeader): void
    {
        if ($token !== null) {
            $this->headers['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        }
    }

    private function mergeArraysUniquely(array $firstArray, array $secondArray): array
    {
        foreach ($secondArray as $key => $value) {
            if (is_string($key) && str_ends_with($key, '[]')) {
                $key = substr($key, 0, -2);
                $firstArray[$key][] = $value;

                continue;
            }

            if (is_array($value) && is_array(@$firstArray[$key])) {
                $value = $this->mergeArraysUniquely($firstArray[$key], $value);
            }
            $firstArray[$key] = $value;
        }

        return $firstArray;
    }
}
