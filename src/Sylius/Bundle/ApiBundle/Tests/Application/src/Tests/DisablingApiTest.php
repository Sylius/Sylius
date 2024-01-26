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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DisablingApiTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles([]);
        $this->setUpTest();
    }

    /** @test */
    public function it_gets_collection_if_api_is_enabled(): void
    {
        $this->enableApi();
        static::createClient()->request(
            'GET',
            'api/v2/admin/channels',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_returns_route_not_found_if_api_is_disabled(): void
    {
        $this->disableApi();
        $this->expectException(NotFoundHttpException::class);

        static::createClient()->request(
            'GET',
            'api/v2/admin/channels',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_throws_not_found_exception_for_any_api_endpoint(): void
    {
        $this->disableApi();
        $this->expectException(NotFoundHttpException::class);

        static::createClient()->request(
            'GET',
            'api/v2/',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    private function disableApi(): void
    {
        putenv('SYLIUS_API_ENABLED=false');
    }

    private function enableApi(): void
    {
        putenv('SYLIUS_API_ENABLED=true');
    }
}
