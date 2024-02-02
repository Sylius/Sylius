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

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\MetadataMergerInterface;

final class LegacyResourceMetadataMergerSpec extends ObjectBehavior
{
    function it_is_a_metadata_merger(): void
    {
        $this->shouldImplement(MetadataMergerInterface::class);
    }

    function it_does_nothing_when_both_new_and_old_metadata_are_epmty(): void
    {
        $this->merge([], [])->shouldReturn([]);
    }

    function it_returns_old_metadata_as_is_when_new_metadata_is_empty(): void
    {
        $this->merge(['foo' => 'bar'], [])->shouldReturn(['foo' => 'bar']);
    }

    function it_returns_new_metadata_as_is_when_old_metadata_is_empty(): void
    {
        $this->merge([], ['foo' => 'bar'])->shouldReturn(['foo' => 'bar']);
    }

    function it_ignores_properties_when_merging(): void
    {
        $this->merge(
            ['properties' => ['foo' => 'bar']],
            ['properties' => ['foo' => 'baz']],
        )->shouldReturn(['properties' => ['foo' => 'bar']]);
    }

    function it_adds_metadata_missing_from_old_metadata_as_they_are(): void
    {
        $this->merge(
            ['foo' => 'bar'],
            ['baz' => 'qux'],
        )->shouldReturn(['foo' => 'bar', 'baz' => 'qux']);
    }

    function it_overrides_metadata_present_in_both_old_and_new_metadata(): void
    {
        $this->merge(
            ['foo' => 'bar'],
            ['foo' => 'baz'],
        )->shouldReturn(['foo' => 'baz']);
    }

    function it_adds_new_collection_operations_when_old_metadata_has_none(): void
    {
        $this->merge(
            [],
            ['collectionOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['collectionOperations' => ['get' => ['baz' => 'qux']]]);
    }

    function it_adds_new_collection_operations_when_old_metadata_did_not_have_them(): void
    {
        $this->merge(
            ['collectionOperations' => ['post' => ['foo' => 'bar']]],
            ['collectionOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['collectionOperations' => [
            'post' => ['foo' => 'bar'],
            'get' => ['baz' => 'qux'],
        ]]);
    }

    function it_merges_collection_operations(): void
    {
        $this->merge([
            'collectionOperations' => [
                'get' => ['foo' => 'bar'],
            ],
        ], [
            'collectionOperations' => [
                'get' => ['baz' => 'qux'],
            ],
        ])->shouldReturn([
            'collectionOperations' => [
                'get' => ['foo' => 'bar', 'baz' => 'qux'],
            ],
        ]);
    }

    function it_adds_new_item_operations_when_old_metadata_has_none(): void
    {
        $this->merge(
            [],
            ['itemOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['itemOperations' => ['get' => ['baz' => 'qux']]]);
    }

    function it_adds_new_item_operations_when_old_metadata_did_not_have_them(): void
    {
        $this->merge(
            ['itemOperations' => ['post' => ['foo' => 'bar']]],
            ['itemOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['itemOperations' => [
            'post' => ['foo' => 'bar'],
            'get' => ['baz' => 'qux'],
        ]]);
    }

    function it_merges_item_operations(): void
    {
        $this->merge([
            'itemOperations' => [
                'get' => ['foo' => 'bar'],
            ],
        ], [
            'itemOperations' => [
                'get' => ['baz' => 'qux'],
            ],
        ])->shouldReturn([
            'itemOperations' => [
                'get' => ['foo' => 'bar', 'baz' => 'qux'],
            ],
        ]);
    }

    function it_adds_new_subresource_operations_when_old_metadata_has_none(): void
    {
        $this->merge(
            [],
            ['subresourceOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['subresourceOperations' => ['get' => ['baz' => 'qux']]]);
    }

    function it_adds_new_subresource_operations_when_old_metadata_did_not_have_them(): void
    {
        $this->merge(
            ['subresourceOperations' => ['post' => ['foo' => 'bar']]],
            ['subresourceOperations' => ['get' => ['baz' => 'qux']]],
        )->shouldReturn(['subresourceOperations' => [
            'post' => ['foo' => 'bar'],
            'get' => ['baz' => 'qux'],
        ]]);
    }

    function it_merges_subresource_operations(): void
    {
        $this->merge([
            'subresourceOperations' => [
                'get' => ['foo' => 'bar'],
            ],
        ], [
            'subresourceOperations' => [
                'get' => ['baz' => 'qux'],
            ],
        ])->shouldReturn([
            'subresourceOperations' => [
                'get' => ['foo' => 'bar', 'baz' => 'qux'],
            ],
        ]);
    }

    function it_merges_complex_metadata(): void
    {
        $this->merge([
            'validation_groups' => ['Default', 'sylius'],
            'route_prefix' => 'old',
            'collectionOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/old',
                    'controller' => 'old',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
            ],
            'itemOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/old/{id}',
                    'controller' => 'old',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
            ],
            'subresourceOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/old/{id}/old',
                    'controller' => 'old',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
            ],
            'properties' => [
                'foo' => [
                    'description' => 'old',
                    'readable' => true,
                    'writable' => true,
                    'required' => true,
                    'identifier' => true,
                ],
            ],
        ], [
            'validation_groups' => 'sylius',
            'collectionOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/new',
                    'controller' => 'new',
                ],
            ],
            'itemOperations' => [
                'post' => [
                    'method' => 'POST',
                    'path' => '/new/{id}',
                    'controller' => 'new',
                ],
            ],
            'properties' => [
                'bar' => [
                    'description' => 'new',
                    'readable' => true,
                    'writable' => true,
                    'required' => false,
                    'readableLink' => true,
                    'identifier' => false,
                ],
            ],
        ])->shouldIterateLike([
            'validation_groups' => 'sylius',
            'route_prefix' => 'old',
            'collectionOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/new',
                    'controller' => 'new',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
            ],
            'itemOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/old/{id}',
                    'controller' => 'old',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
                'post' => [
                    'method' => 'POST',
                    'path' => '/new/{id}',
                    'controller' => 'new',
                ],
            ],
            'subresourceOperations' => [
                'get' => [
                    'method' => 'GET',
                    'path' => '/old/{id}/old',
                    'controller' => 'old',
                    'defaults' => ['_controller' => 'old'],
                    'requirements' => ['_format' => 'old'],
                    'options' => ['foo' => 'bar'],
                    'openapi_context' => ['foo' => 'bar'],
                ],
            ],
            'properties' => [
                'foo' => [
                    'description' => 'old',
                    'readable' => true,
                    'writable' => true,
                    'required' => true,
                    'identifier' => true,
                ],
            ],
        ]);
    }
}
