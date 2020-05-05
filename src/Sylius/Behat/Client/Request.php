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

use Symfony\Component\HttpFoundation\Request as HttpRequest;

final class Request implements RequestInterface
{
    /** @var string */
    private $url;

    /** @var string */
    private $method;

    /** @var array */
    private $headers = ['HTTP_ACCEPT' => 'application/ld+json'];

    /** @var array */
    private $content = [];

    /** @var array */
    private $parameters = [];

    /** @var array */
    private $files = [];

    private function __construct(string $url, string $method, array $headers = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = array_merge($this->headers, $headers);
    }

    public static function index(string $resource, string $token): RequestInterface
    {
        return new self('/new-api/' . $resource, HttpRequest::METHOD_GET, ['HTTP_Authorization' => 'Bearer ' . $token]);
    }

    public static function subResourceIndex(string $resource, string $id, string $subResource, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s/%s/%s', $resource, $id, $subResource),
            HttpRequest::METHOD_GET,
            ['HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function show(string $resource, string $id, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s/%s', $resource, $id),
            HttpRequest::METHOD_GET,
            ['HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function create(string $resource, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s', $resource),
            HttpRequest::METHOD_POST,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function update(string $resource, string $id, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s/%s', $resource, $id),
            HttpRequest::METHOD_PUT,
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function delete(string $resource, string $id, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s/%s', $resource, $id),
            HttpRequest::METHOD_DELETE,
            ['HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function transition(string $resource, string $id, string $transition, string $token): RequestInterface
    {
        return self::customItemAction($resource, $id, HttpRequest::METHOD_PATCH, $transition, $token);
    }

    public static function customItemAction(string $resource, string $id, string $type, string $action, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s/%s/%s', $resource, $id, $action),
            $type,
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function upload(string $resource, string $token): RequestInterface
    {
        return new self(
            sprintf('/new-api/%s', $resource),
            HttpRequest::METHOD_POST,
            ['CONTENT_TYPE' => 'multipart/form-data', 'HTTP_Authorization' => 'Bearer ' . $token]
        );
    }

    public static function custom(string $url, string $method, ?string $token): RequestInterface
    {
        return new self(
            $url,
            $method,
            ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_Authorization' => 'Bearer ' . $token]
        );
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

    public function files(): array
    {
        return $this->files;
    }

    public function updateFiles(array $newFiles): void
    {
        $this->files = array_merge($this->files, $newFiles);
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

    private function mergeArraysUniquely(array $firstArray, array $secondArray): array
    {
        foreach ($secondArray as $key => $value) {
            if (is_array($value) && is_array(@$firstArray[$key])) {
                $value = $this->mergeArraysUniquely($firstArray[$key], $value);
            }
            $firstArray[$key] = $value;
        }

        return $firstArray;
    }
}
