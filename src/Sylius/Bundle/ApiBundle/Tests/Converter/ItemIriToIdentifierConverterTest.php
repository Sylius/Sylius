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
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverter;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

final class ItemIriToIdentifierConverterTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_no_route_matches(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No route matches "/users/3".');

        $router = $this->prophesize(RouterInterface::class);
        $router->match('/users/3')->willThrow(new RouteNotFoundException())->shouldBeCalledTimes(1);

        $converter = new ItemIriToIdentifierConverter($router->reveal());
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
        $router->match('/users/3')->willReturn([])->shouldBeCalledTimes(1);

        $converter = new ItemIriToIdentifierConverter($router->reveal());
        $converter->getIdentifier('/users/3');
    }

    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_parameter_id_does_not_exist(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter "id" not found');

        $router = $this->prophesize(RouterInterface::class);
        $router->match('/users')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_item_operation_name' => 'get',
            '_api_identifiers' => [],
        ])->shouldBeCalledTimes(1);

        $converter = new ItemIriToIdentifierConverter($router->reveal());
        $converter->getIdentifier('/users');
    }

    /**
     * @test
     */
    public function it_get_identifier(): void
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->match('/users/3')->willReturn([
            '_api_resource_class' => AddProductReview::class,
            '_api_item_operation_name' => 'get',
            '_api_identifiers' => ['id'],
            'id' => 3,
        ])->shouldBeCalledTimes(1);

        $converter = new ItemIriToIdentifierConverter($router->reveal());

        $this->assertSame('3', $converter->getIdentifier('/users/3'));
    }
}
