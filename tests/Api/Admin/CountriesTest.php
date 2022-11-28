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
        $header = $this->getLoggedHeader();

        /** @var CountryInterface $country */
        $country = $fixtures['country_DE'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/countries/%s', $country->getCode()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/countries',
            [],
            [],
            $header,
        );

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
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/countries',
            [],
            [],
            $header,
            json_encode([
                'code' => 'IE',
                'enabled' => true,
            ], JSON_THROW_ON_ERROR),
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

        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            '/api/v2/admin/countries/' . $country->getCode(),
            [],
            [],
            $header,
            json_encode([
                'enabled' => false,
                'provinces' => [[
                    'code' => 'US-WA',
                    'name' => 'Washington',
                    'country' => $country->getCode(),
                ]]
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/country/put_country_response',
            Response::HTTP_OK,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
