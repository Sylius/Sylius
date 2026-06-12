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

namespace Sylius\Tests\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(operations: [
    new GetCollection(
        uriTemplate: '/admin/sample-products',
        name: 'sylius_api_admin_sample_product_get_collection',
        provider: SampleProductProvider::class,
    ),
    new Get(
        uriTemplate: '/admin/sample-products/{id}',
        name: 'sylius_api_admin_sample_product_get',
        provider: SampleProductProvider::class,
    ),
    new GetCollection(
        uriTemplate: '/shop/sample-products',
        name: 'sylius_api_shop_sample_product_get_collection',
        provider: SampleProductProvider::class,
    ),
    new Get(
        uriTemplate: '/shop/sample-products/{id}',
        name: 'sylius_api_shop_sample_product_get',
        provider: SampleProductProvider::class,
    ),
])]
class SampleProduct
{
    public function __construct(
        private int $id,
        private string $name,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
