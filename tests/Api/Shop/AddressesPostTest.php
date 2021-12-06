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

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AddressesPostTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_a_create_an_address_for_not_authenticated_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_new_address_for_logged_customer(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/customer.yaml', 'country.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];
        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $this->client->request(
            'POST',
            '/api/v2/shop/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $customer->getEmailCanonical(), 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        $bodyRequest = $this->createBodyRequest($country->getCode());

        $this->client->request(
            'POST',
            '/api/v2/shop/addresses',
            [],
            [],
            array_merge($header, ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/json']),
            json_encode($bodyRequest)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/create_address_response', Response::HTTP_CREATED);
    }

    private function createBodyRequest(string $countryCode): array
    {
        return [
            'firstName'=> 'TEST',
            'lastName'=> 'TEST',
            'phoneNumber'=> '666111333',
            'company'=> 'Potato Corp.',
            'countryCode'=> $countryCode,
            'provinceCode'=> null,
            'provinceName'=> null,
            'street'=> 'Top secret',
            'city'=> 'Nebraska',
            'postcode'=> '12343'
        ];
    }
}
