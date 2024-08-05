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

namespace Sylius\Bundle\ApiBundle\Tests\Converter;

use ApiPlatform\Api\UriVariablesConverterInterface;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverter;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException as SymfonyRouteNotFoundException;
use Sylius\Bundle\ApiBundle\Exception\NoRouteMatchesException as ApiRouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

final class IriToIdentifierConverterTest extends TestCase
{
    use ProphecyTrait;

    private RouterInterface|ObjectProphecy $router;

    private ResourceMetadataCollectionFactoryInterface|ObjectProphecy $metadataFactory;

    private UriVariablesConverterInterface|ObjectProphecy $uriVariablesConverter;

    private IriToIdentifierConverterInterface $converter;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->metadataFactory = $this->prophesize(ResourceMetadataCollectionFactoryInterface::class);
        $this->uriVariablesConverter = $this->prophesize(UriVariablesConverterInterface::class);

        $this->converter = new IriToIdentifierConverter(
            $this->router->reveal(),
            $this->metadataFactory->reveal(),
            $this->uriVariablesConverter->reveal(),
        );
    }

    /**
     * @test
     *
     * @dataProvider invalidIdentifierValues
     */
    public function it_treats_non_string_values_as_not_identifiers(mixed $invalidValue): void
    {
        $this->router->match(Argument::any())->shouldNotBeCalled();

        $this->assertFalse($this->converter->isIdentifier($invalidValue));
    }

    /** @test */
    public function it_treats_not_matched_strings_as_not_identifiers(): void
    {
        $this->router->match('test')->willThrow(new SymfonyRouteNotFoundException());

        $this->assertFalse($this->converter->isIdentifier('test'));
    }

    /** @test */
    public function it_treats_strings_matched_on_routes_with_no_resource_class_parameter_as_not_identifiers(): void
    {
        $this->router->match('test')->willReturn([]);

        $this->assertFalse($this->converter->isIdentifier('test'));
    }

    /** @test */
    public function it_treats_strings_matched_on_routes_with_resource_class_parameter_as_identifiers(): void
    {
        $this->router->match('test')->willReturn([
            '_api_resource_class' => 'test',
        ]);

        $this->assertTrue($this->converter->isIdentifier('test'));
    }

    /** @test */
    public function it_throws_invalid_argument_exception_if_no_route_matches(): void
    {
        $this->expectException(ApiRouteNotFoundException::class);
        $this->expectExceptionMessage('No route matches "/users/3".');

        $this->router->match('/users/3')->willThrow(new SymfonyRouteNotFoundException())->shouldBeCalledTimes(1);

        $this->converter->getIdentifier('/users/3');
    }

    /** @test */
    public function it_throws_invalid_argument_exception_if_parameter_api_resource_class_does_not_exist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No resource associated to "/users/3".');

        $this->router->match('/users/3')->willReturn([
            '_api_operation_name' => 'get',
        ])->shouldBeCalledTimes(1);

        $this->converter->getIdentifier('/users/3');
    }

    /** @test */
    public function it_throws_invalid_argument_exception_if_parameter_api_operation_name_does_not_exist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No resource associated to "/users/3".');

        $this->router->match('/users/3')->willReturn([
            '_api_resource_class' => AddProductReview::class,
        ])->shouldBeCalledTimes(1);

        $this->converter->getIdentifier('/users/3');
    }

    /** @test */
    public function it_throws_invalid_argument_exception_if_converter_returns_more_than_one_identifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IriToIdentifierConverter does not support subresources');

        $operation = $this->prophesize(HttpOperation::class);
        $operation->getClass()->willReturn(AddProductReview::class);
        $operation->getUriVariables()->willReturn([
            'id' => new Link('id', identifiers: ['id'], compositeIdentifier: true),
            'nextId' => new Link('nextId', identifiers: ['nextId'], compositeIdentifier: true),
        ]);

        $this->router->match('/users/3/nexts/5')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_operation_name' => 'get',
            'id' => 3,
            'nextId' => 5,
        ])->shouldBeCalledTimes(1);

        $this->uriVariablesConverter->convert(
            ['id' => 3, 'nextId' => 5],
            AddProductReview::class,
            Argument::cetera(),
        )->willReturn(['3', '5']);

        $this->converter->getIdentifier('/users/3/nexts/5', $operation->reveal());
    }

    /** @test */
    public function it_gets_identifier(): void
    {
        $operation = $this->prophesize(HttpOperation::class);
        $operation->getClass()->willReturn(AddProductReview::class);
        $operation->getUriVariables()->willReturn([
            'id' => new Link('id', identifiers: ['id'], compositeIdentifier: true),
        ]);

        $this->router->match('/users/3')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_operation_name' => 'get',
            'id' => 3,
        ])->shouldBeCalledTimes(1);

        $this->uriVariablesConverter->convert(
            ['id' => 3],
            AddProductReview::class,
            Argument::cetera(),
        )->willReturn(['3']);

        $this->assertSame('3', $this->converter->getIdentifier('/users/3', $operation->reveal()));
    }

    public function invalidIdentifierValues(): iterable
    {
        yield [0];
        yield [0.1];
        yield [null];
        yield [new \stdClass()];
    }
}
