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

use Symfony\Component\HttpFoundation\Request as HttpRequest;

final class RequestFactory implements RequestFactoryInterface
{
    private const LINKED_DATA_JSON_CONTENT_TYPE = 'application/ld+json';

    private const UPLOAD_FILE_CONTENT_TYPE = 'multipart/form-data';

    public function __construct(
        private ContentTypeGuideInterface $contentTypeGuide,
        private string $apiUrlPrefix,
    ) {
    }

    public function index(
        ?string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s', $this->apiUrlPrefix, $section, $resource),
            HttpRequest::METHOD_GET,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder->build();
    }

    public function subResourceIndex(string $section, string $resource, string $id, string $subResource): RequestInterface
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s/%s/%s', $this->apiUrlPrefix, $section, $resource, $id, $subResource),
            HttpRequest::METHOD_GET,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        return $builder->build();
    }

    public function show(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s/%s', $this->apiUrlPrefix, $section, $resource, $id),
            HttpRequest::METHOD_GET,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder->build();
    }

    public function create(
        string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s', $this->apiUrlPrefix, $section, $resource),
            HttpRequest::METHOD_POST,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder->build();
    }

    public function update(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s/%s', $this->apiUrlPrefix, $section, $resource, $id),
            HttpRequest::METHOD_PUT,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide(HttpRequest::METHOD_PUT));

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder->build();
    }

    public function delete(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s/%s', $this->apiUrlPrefix, $section, $resource, $id),
            HttpRequest::METHOD_DELETE,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder->build();
    }

    public function transition(string $section, string $resource, string $id, string $transition): RequestInterface
    {
        return $this->customItemAction($section, $resource, $id, HttpRequest::METHOD_PATCH, $transition);
    }

    public function customItemAction(string $section, string $resource, string $id, string $type, string $action): RequestInterface
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s/%s/%s', $this->apiUrlPrefix, $section, $resource, $id, $action),
            $type,
        );
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide($type));

        return $builder->build();
    }

    public function upload(
        string $section,
        string $resource,
        array $files,
        string $authorizationHeader,
        ?string $token = null,
    ): RequestInterface {
        $builder = RequestBuilder::create(
            sprintf('%s/%s/%s', $this->apiUrlPrefix, $section, $resource),
            HttpRequest::METHOD_POST,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', self::UPLOAD_FILE_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        foreach ($files as $name => $value) {
            $builder->withFile($name, $value);
        }

        return $builder->build();
    }

    public function custom(string $url, string $method, array $additionalHeaders = [], ?string $token = null): RequestInterface
    {
        $builder = RequestBuilder::create($url, $method);
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide($method));

        if ($token) {
            $builder->withHeader('HTTP_Authorization', 'Bearer ' . $token);
        }

        foreach ($additionalHeaders as $name => $value) {
            $builder->withHeader($name, $value);
        }

        return $builder->build();
    }
}
