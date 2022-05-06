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
        private string $apiPrefix,
    ) {
    }

    public function get(string ...$resources): RequestBuilder
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s', $this->apiPrefix, implode('/', $resources)),
            HttpRequest::METHOD_GET,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        return $builder;
    }

    public function post(string ...$resources): RequestBuilder
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s', $this->apiPrefix, implode('/', $resources)),
            HttpRequest::METHOD_POST,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', self::LINKED_DATA_JSON_CONTENT_TYPE);

        return $builder;
    }

    public function put(string ...$resources): RequestBuilder
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s', $this->apiPrefix, implode('/', $resources)),
            HttpRequest::METHOD_PUT,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide(HttpRequest::METHOD_PUT));

        return $builder;
    }

    public function patch(string ...$resources): RequestBuilder
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s', $this->apiPrefix, implode('/', $resources)),
            HttpRequest::METHOD_PATCH,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);
        $builder->withHeader('CONTENT_TYPE', $this->contentTypeGuide->guide(HttpRequest::METHOD_PATCH));

        return $builder;
    }

    public function delete(string ...$resources): RequestBuilder
    {
        $builder = RequestBuilder::create(
            sprintf('%s/%s', $this->apiPrefix, implode('/', $resources)),
            HttpRequest::METHOD_DELETE,
        );
        $builder->withHeader('HTTP_ACCEPT', self::LINKED_DATA_JSON_CONTENT_TYPE);

        return $builder;
    }
}
