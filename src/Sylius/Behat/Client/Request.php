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
    private string $url;

    private string $method;

    private array $headers = ['HTTP_ACCEPT' => 'application/ld+json'];

    private array $content = [];

    private array $parameters = [];

    private array $files = [];

    private function __construct(string $url, string $method, array $headers = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->headers = array_merge($this->headers, $headers);
    }

    public static function index(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = $token ? ['HTTP_' . $authorizationHeader => 'Bearer ' . $token] : [];

        return new self(
            sprintf('/api/v2/%s%s', self::prepareSection($section), $resource),
            HttpRequest::METHOD_GET,
            $headers
        );
    }

    public static function subResourceIndex(?string $section, string $resource, string $id, string $subResource): RequestInterface
    {
        return new self(
            sprintf('/api/v2/%s%s/%s/%s', self::prepareSection($section), $resource, $id, $subResource),
            HttpRequest::METHOD_GET
        );
    }

    public static function show(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = $token ? ['HTTP_' . $authorizationHeader => 'Bearer ' . $token] : [];

        return new self(
            sprintf('/api/v2/%s%s/%s', self::prepareSection($section), $resource, $id),
            HttpRequest::METHOD_GET,
            $headers
        );
    }

    public static function create(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = ['CONTENT_TYPE' => 'application/ld+json'];
        if ($token !== null) {
            $headers['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        }

        return new self(
            sprintf('/api/v2/%s%s', self::prepareSection($section), $resource),
            HttpRequest::METHOD_POST,
            $headers
        );
    }

    public static function update(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = ['CONTENT_TYPE' => 'application/ld+json'];
        if ($token !== null) {
            $headers['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        }

        return new self(
            sprintf('/api/v2/%s%s/%s', self::prepareSection($section), $resource, $id),
            HttpRequest::METHOD_PUT,
            $headers
        );
    }

    public static function delete(
        ?string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = $token ? ['HTTP_' . $authorizationHeader => 'Bearer ' . $token] : [];

        return new self(
            sprintf('/api/v2/%s%s/%s', self::prepareSection($section), $resource, $id),
            HttpRequest::METHOD_DELETE,
            $headers
        );
    }

    public static function transition(?string $section, string $resource, string $id, string $transition): RequestInterface
    {
        return self::customItemAction($section, $resource, $id, HttpRequest::METHOD_PATCH, $transition);
    }

    public static function customItemAction(?string $section, string $resource, string $id, string $type, string $action): RequestInterface
    {
        return new self(
            sprintf('/api/v2/%s%s/%s/%s', self::prepareSection($section), $resource, $id, $action),
            $type,
            ['CONTENT_TYPE' => self::resolveHttpMethod($type)]
        );
    }

    public static function upload(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null
    ): RequestInterface {
        $headers = ['CONTENT_TYPE' => 'multipart/form-data'];
        if ($token !== null) {
            $headers['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        }

        return new self(
            sprintf('/api/v2/%s%s', self::prepareSection($section), $resource),
            HttpRequest::METHOD_POST,
            $headers
        );
    }

    public static function custom(string $url, string $method, ?string $token = null): RequestInterface
    {
        $headers = ['CONTENT_TYPE' => self::resolveHttpMethod($method)];
        if ($token !== null) {
            $headers['HTTP_Authorization'] = 'Bearer ' . $token;
        }

        return new self($url, $method, $headers);
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
            if (is_array($value) && is_array(@$firstArray[$key])) {
                $value = $this->mergeArraysUniquely($firstArray[$key], $value);
            }
            $firstArray[$key] = $value;
        }

        return $firstArray;
    }

    private static function prepareSection(?string $section): string
    {
        if ($section === null) {
            return '';
        }

        return $section . '/';
    }

    private static function resolveHttpMethod(string $method): string
    {
        return $method === HttpRequest::METHOD_PATCH ? 'application/merge-patch+json' : 'application/json';
    }
}
