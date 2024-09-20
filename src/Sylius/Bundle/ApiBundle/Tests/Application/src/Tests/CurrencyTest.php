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

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

final class CurrencyTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['Tests/Application/config/fixtures/currency.yaml']);
        $this->setUpTest();
    }

    /** @test */
    public function it_gets_currencies_with_custom_field(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/currencies',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Currency',
            '@id' => '/api/v2/admin/currencies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@id' => '/api/v2/admin/currencies/USD',
                '@type' => 'Currency',
                'type' => 'default',
                'code' => 'USD',
            ], [
                '@id' => '/api/v2/admin/currencies/GBP',
                '@type' => 'Currency',
                'type' => 'test_GBP',
                'code' => 'GBP',
            ], [
                '@id' => '/api/v2/admin/currencies/EUR',
                '@type' => 'Currency',
                'type' => 'test_EUR',
                'code' => 'EUR',
            ]],
        ]);
    }
}
