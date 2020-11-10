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

namespace Sylius\Bundle\ApiBundle\Tests\Converter;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Identifier\IdentifierConverterInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverter;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

final class IriToIdentifierConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_no_route_matches(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No route matches "/users/3".');

        $router = $this->prophesize(RouterInterface::class);
        $identifierConverter = $this->prophesize(IdentifierConverterInterface::class);

        $router->match('/users/3')->willThrow(new RouteNotFoundException())->shouldBeCalledTimes(1);

        $converter = new IriToIdentifierConverter($router->reveal(), $identifierConverter->reveal());
        $converter->getIdentifier('/users/3');
    }

    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_parameter_api_resource_class_does_not_exist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No resource associated to "/users/3".');

        $router = $this->prophesize(RouterInterface::class);
        $identifierConverter = $this->prophesize(IdentifierConverterInterface::class);

        $router->match('/users/3')->willReturn([])->shouldBeCalledTimes(1);

        $converter = new IriToIdentifierConverter($router->reveal(), $identifierConverter->reveal());
        $converter->getIdentifier('/users/3');
    }

    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_converter_returns_more_than_one_identifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('IriToIdentifierConverter does not support subresources');

        $router = $this->prophesize(RouterInterface::class);
        $identifierConverter = $this->prophesize(IdentifierConverterInterface::class);

        $router->match('/users/3/nexts/5')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_item_operation_name' => 'get',
            '_api_identifiers' => ['id', 'nextId'],
            'id' => 3,
            'nextId' => 5,
        ])->shouldBeCalledTimes(1);

        $identifierConverter->convert(['id' => 3, 'nextId' => 5], AddProductReview::class)->willReturn(['3', '5']);
        $converter = new IriToIdentifierConverter($router->reveal(), $identifierConverter->reveal());

        $this->assertSame('3', $converter->getIdentifier('/users/3/nexts/5'));
    }

    /**
     * @test
     */
    public function it_gets_identifier(): void
    {
        $router = $this->prophesize(RouterInterface::class);
        $identifierConverter = $this->prophesize(IdentifierConverterInterface::class);

        $router->match('/users/3')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_item_operation_name' => 'get',
            '_api_identifiers' => ['id'],
            'id' => 3,
        ])->shouldBeCalledTimes(1);

        $identifierConverter->convert(['id' => 3], AddProductReview::class)->willReturn(['3']);
        $converter = new IriToIdentifierConverter($router->reveal(), $identifierConverter->reveal());

        $this->assertSame('3', $converter->getIdentifier('/users/3'));
    }
}
