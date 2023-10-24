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

namespace Sylius\Bundle\ApiBundle\Tests\Resolver;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface;
use Sylius\Bundle\ApiBundle\Resolver\OperationResolverInterface;
use Sylius\Bundle\ApiBundle\Resolver\PathPrefixBasedOperationResolver;
use Sylius\Component\Addressing\Model\Province;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PathPrefixBasedOperationResolverTest extends KernelTestCase
{
    use ProphecyTrait;

    private PathPrefixProviderInterface|ObjectProphecy $pathPrefixProvider;

    private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory;

    private OperationResolverInterface $operationResolver;

    protected function setUp(): void
    {
        $this->pathPrefixProvider = $this->prophesize(PathPrefixProviderInterface::class);
        $this->resourceMetadataCollectionFactory = $this->getResourceMetadataCollectionFactory();
        $this->operationResolver = new PathPrefixBasedOperationResolver($this->resourceMetadataCollectionFactory, $this->pathPrefixProvider->reveal());
    }

    /** @test */
    public function it_provides_shop_operation_if_request_prefix_is_shop(): void
    {
        $this->pathPrefixProvider->getPathPrefix('api/v2/shop/countries')->willReturn('shop');
        $operation = $this->operationResolver->resolve(Province::class, 'api/v2/shop/countries', null);

        $this->assertSame('/shop/provinces/{code}', $operation->getUriTemplate());
    }

    /** @test */
    public function it_provides_admin_operation_if_request_prefix_is_admin(): void
    {
        $this->pathPrefixProvider->getPathPrefix('api/v2/admin/countries')->willReturn('admin');
        $operation = $this->operationResolver->resolve(Province::class, 'api/v2/admin/countries', null);

        $this->assertSame('/admin/provinces/{code}', $operation->getUriTemplate());
    }

    private function getResourceMetadataCollectionFactory(): ResourceMetadataCollectionFactoryInterface
    {
        return self::getContainer()->get('api_platform.metadata.resource.metadata_collection_factory');
    }
}
