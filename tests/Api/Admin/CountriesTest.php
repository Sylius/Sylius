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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CountriesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_country(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'country.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/countries/%s', $country->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/country/get_country_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_countries(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'country.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/countries', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/country/get_countries_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_country(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/countries',
            server: $header,
            content: json_encode([
                'code' => 'IE',
                'enabled' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/country/post_country_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_country(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'country.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/countries/' . $country->getCode(),
            server: $header,
            content: json_encode([
                'enabled' => false,
                'provinces' => [[
                    'code' => 'US-WA',
                    'name' => 'Washington',
                    'country' => $country->getCode(),
                ]],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/country/put_country_response',
            Response::HTTP_OK,
        );
    }
}
