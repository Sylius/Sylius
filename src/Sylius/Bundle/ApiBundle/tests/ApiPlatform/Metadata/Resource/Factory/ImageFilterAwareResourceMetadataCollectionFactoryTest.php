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

namespace Tests\Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Operation as GraphQlOperation;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Resource\Factory\ImageFilterAwareResourceMetadataCollectionFactory;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ImageInterface;

final class ImageFilterAwareResourceMetadataCollectionFactoryTest extends TestCase
{
    private MockObject&ResourceMetadataCollectionFactoryInterface $decorated;

    private ImageFilterAwareResourceMetadataCollectionFactory $factory;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(ResourceMetadataCollectionFactoryInterface::class);

        $this->factory = new ImageFilterAwareResourceMetadataCollectionFactory(
            $this->decorated,
            [ImageInterface::class],
        );
    }

    #[Test]
    public function it_returns_unchanged_metadata_when_resource_is_not_image_related(): void
    {
        $resourceClass = \stdClass::class;
        $resourceMetadataCollection = $this->createMetadataCollection($resourceClass, [
            'get' => [
                'operationName' => 'get',
                'operationType' => Get::class,
            ],
            'get_collection' => [
                'operationName' => 'get_collection',
                'operationType' => GetCollection::class,
                'parameters' => [ImageNormalizer::FILTER_QUERY_PARAMETER],
            ],
        ]);

        $this->decorated->expects($this->once())
            ->method('create')
            ->with($resourceClass)
            ->willReturn($resourceMetadataCollection)
        ;

        $this->assertSame($resourceMetadataCollection, $this->factory->create($resourceClass));
    }

    #[Test]
    public function it_returns_unchanged_metadata_when_resource_has_no_operations(): void
    {
        $resourceClass = Image::class;
        $resourceMetadataCollection = $this->createMetadataCollection($resourceClass, []);

        $this->decorated->expects($this->once())
            ->method('create')
            ->with($resourceClass)
            ->willReturn($resourceMetadataCollection)
        ;

        $this->assertSame($resourceMetadataCollection, $this->factory->create($resourceClass));
    }

    #[DataProvider('getUnhandledOperations')]
    #[Test]
    public function it_returns_unchanged_metadata_when_resource_has_only_unhandled_operations(
        string $operationClass,
    ): void {
        $resourceClass = Image::class;
        $resourceMetadataCollection = $this->createMetadataCollection($resourceClass, [
            $operationClass => [
                'operationName' => $operationClass,
                'operationType' => $operationClass,
            ],
        ]);

        $this->decorated->expects($this->once())
            ->method('create')
            ->with($resourceClass)
            ->willReturn($resourceMetadataCollection)
        ;

        $this->assertSame($resourceMetadataCollection, $this->factory->create($resourceClass));
    }

    #[DataProvider('getHandledOperations')]
    #[Test]
    public function it_returns_unchanged_metadata_when_resource_already_has_image_filter_parameter(
        string $operationClass,
    ): void {
        $resourceClass = Image::class;
        $resourceMetadataCollection = $this->createMetadataCollection($resourceClass, [
            $operationClass => [
                'operationName' => $operationClass,
                'operationType' => $operationClass,
                'parameters' => [ImageNormalizer::FILTER_QUERY_PARAMETER],
            ],
        ]);

        $this->decorated->expects($this->once())
            ->method('create')
            ->with($resourceClass)
            ->willReturn($resourceMetadataCollection)
        ;

        $this->assertSame($resourceMetadataCollection, $this->factory->create($resourceClass));
    }

    #[DataProvider('getHandledOperationsWithParameters')]
    #[Test]
    public function it_adds_filter_parameter_to_handled_operations_when_missing(
        string $operationClass,
        array $operationParameters,
    ): void {
        $resourceClass = Image::class;
        $originalResourceMetadataCollection = $this->createMetadataCollection($resourceClass, [
            $operationClass => [
                'operationName' => $operationClass,
                'operationType' => $operationClass,
                'parameters' => $operationParameters,
            ],
        ]);

        $this->decorated->expects($this->once())
            ->method('create')
            ->with($resourceClass)
            ->willReturn($originalResourceMetadataCollection)
        ;

        $result = $this->factory->create($resourceClass);
        $resultOperation = $result->getOperation($operationClass);

        $joinedParameters = array_merge($operationParameters, [ImageNormalizer::FILTER_QUERY_PARAMETER]);

        $this->assertNotEmpty(iterator_to_array($resultOperation->getParameters()));

        foreach ($joinedParameters as $joinedParameter) {
            $this->assertTrue(
                $resultOperation->getParameters()->has($joinedParameter),
                sprintf(
                    'Parameter "%s" should be present in the operation "%s".',
                    $joinedParameter,
                    $operationClass,
                ),
            );
        }
    }

    public static function getUnhandledOperations(): iterable
    {
        yield 'GraphQL operation' => [GraphQlOperation::class];
        yield 'Custom operation' => [HttpOperation::class];
        yield 'Delete operation' => [Delete::class];
        yield 'Patch operation' => [Patch::class];
        yield 'Post operation' => [Post::class];
        yield 'Put operation' => [Put::class];
    }

    public static function getHandledOperations(): iterable
    {
        yield 'Get operation' => [Get::class];
        yield 'Get collection operation' => [GetCollection::class];
    }

    public static function getHandledOperationsWithParameters(): iterable
    {
        yield 'Get operation without parameters' => [Get::class, []];
        yield 'Get operation with parameters' => [Get::class, ['another_parameter']];
        yield 'Get collection operation without parameters' => [GetCollection::class, []];
        yield 'Get collection operation with parameters' => [GetCollection::class, ['another_parameter']];
    }

    /**
     * @param array<string, array{
     *     operationName: string,
     *     operationType: class-string,
     *     parameters?: string[],
     * }> $operations
     */
    private function createMetadataCollection(string $resourceClass, array $operations): ResourceMetadataCollection
    {
        $operationsArray = [];
        foreach ($operations as $operationData) {
            /** @var HttpOperation $operation */
            $operation = new $operationData['operationType']();

            $operationParameters = $operationData['parameters'] ?? [];
            if ([] !== $operationParameters) {
                $parameters = [];
                foreach ($operationParameters as $parameter) {
                    $parameters[$parameter] = new QueryParameter(
                        key: $parameter,
                    );
                }

                $operation = $operation->withParameters($parameters);
            }

            $operationsArray[$operationData['operationName']] = $operation;
        }

        $apiResource = new ApiResource(
            operations: $operationsArray,
            class: $resourceClass,
        );

        return new ResourceMetadataCollection($resourceClass, [$apiResource]);
    }
}
