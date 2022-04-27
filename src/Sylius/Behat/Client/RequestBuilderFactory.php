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

final class RequestBuilderFactory implements RequestBuilderFactoryInterface
{
    private const LINKED_DATA_JSON_CONTENT_TYPE = 'application/ld+json';

    public function __construct(
        private ContentTypeGuideInterface $contentTypeGuide,
    ) {
    }

    public function show(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/%s/%s/%s', $section, $resource, $id),
            HttpRequest::METHOD_GET,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder;
    }

    public function create(
        string $section,
        string $resource,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/%s/%s', $section, $resource),
            HttpRequest::METHOD_POST,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', self::LINKED_DATA_JSON_CONTENT_TYPE);

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder;
    }

    public function update(
        string $section,
        string $resource,
        string $id,
        string $authorizationHeader,
        ?string $token = null
    ): RequestBuilder {
        $builder = RequestBuilder::create(
            sprintf('/api/v2/%s/%s/%s', $section, $resource, $id),
            HttpRequest::METHOD_PUT,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide(HttpRequest::METHOD_PUT));

        if ($token) {
            $builder->withHeader('HTTP_' . $authorizationHeader, 'Bearer ' . $token);
        }

        return $builder;
    }
}
