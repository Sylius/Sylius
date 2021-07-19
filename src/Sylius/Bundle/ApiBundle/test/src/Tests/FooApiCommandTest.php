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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class FooApiCommandTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles([]);
        $this->setUpTest();
    }

    /** @test */
    public function it_returns_information_about_missing_arguments_for_command(): void
    {
        static::createClient()->request(
            'POST',
            'api/v2/foo-api-command',
            ['json' => ['name' => 'FooCommandPost']]
        );
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => 'Request does not have the following required fields specified: bar.'
        ]);
    }

    /** @test */
    public function it_allows_using_command_if_every_required_parameter_is_provided(): void
    {
        static::createClient()->request(
            'POST',
            'api/v2/foo-api-command',
            ['json' => ['bar' => 'FooCommandPost']]
        );
        $this->assertResponseIsSuccessful();
    }
}
