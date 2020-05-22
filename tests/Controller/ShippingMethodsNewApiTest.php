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

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodsNewApiTest extends JsonApiTestCase
{
    static private $authorizedHeaderWithDenied = [
        'HTTP_Authorization' => 'Bearer ' . 'wrong_token',
        'HTTP_ACCEPT' => 'application/ld+json',
        'CONTENT_TYPE' => 'application/ld+json',
    ];

    /**
     * @test
     */
    public function it_denies_to_get_shipping_methods_collection_for_not_authenticated_users(): void
    {
        $this->client->request('GET', $this->getShippingMethodUrl(), [], [], self::$authorizedHeaderWithDenied);

        $this->assertResponse(
            $this->client->getResponse(),
            'api/authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_get_shipping_methods_collection_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();
        $this->loadFixturesAndGetShippingMethod('ups');

        $this->client->request(
            'GET',
            $this->getShippingMethodUrl(),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/shipping_method/index_response',
            Response::HTTP_OK
        );
    }

    /**
     * @test
     */
    public function it_denies_to_get_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            'GET',
            $this->getShippingMethodUrl($this->loadFixturesAndGetShippingMethod('ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_get_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();
        $shippingMethod = $this->loadFixturesAndGetShippingMethod('ups');

        $this->client->request(
            'GET',
            $this->getShippingMethodUrl($shippingMethod),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/shipping_method/show_response',
            Response::HTTP_OK
        );
    }

    /**
     * @test
     */
    public function it_denies_to_post_shipping_method_for_not_authenticated_users(): void
    {
        $data =
<<<EOT
        {
            "zone": "\/new-api\/zones\/EU",
            "code": "UPS",
            "position": 0,
            "calculator": "flat_rate",
            "enabled": true,
            "name": "InPost"
        }
EOT;

        $this->client->request(
            'POST',
            $this->getShippingMethodUrl(),
            [],
            [],
            self::$authorizedHeaderWithDenied, $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_post_shipping_method_for_authenticated_users(): void
    {
        $this->loadFixturesFromFiles(['resources/zones.yml']);
        $token = $this->logInAsAdminAndGetToken();

        $data =
<<<EOT
        {
            "zone": "\/new-api\/zones\/EU",
            "code": "UPS",
            "position": 0,
            "calculator": "flat_rate",
            "enabled": true,
            "name": "InPost"
        }
EOT;

        $this->client->request(
            'POST',
            $this->getShippingMethodUrl(),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token),
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/shipping_method/create_item_response',
            Response::HTTP_CREATED
        );
    }

    /**
     * @test
     */
    public function it_denies_to_put_shipping_method_for_not_authenticated_users(): void
    {
        $data =
<<<EOT
        {
            "name": "InPost"
        }
EOT;

        $this->client->request(
            'PUT',
            $this->getShippingMethodUrl($this->loadFixturesAndGetShippingMethod('ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied,
            $data
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_put_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $data =
<<<EOT
        {
            "name": "InPost"
        }
EOT;

        $this->client->request(
            'PUT',
            $this->getShippingMethodUrl($this->loadFixturesAndGetShippingMethod('ups')),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token),
            $data
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_to_delete_shipping_method_for_not_authenticated_users(): void
    {
        $this->client->request(
            'DELETE',
            $this->getShippingMethodUrl($this->loadFixturesAndGetShippingMethod('ups')),
            [],
            [],
            self::$authorizedHeaderWithDenied
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'api/authentication/access_denied_response',
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * @test
     */
    public function it_accepts_to_delete_shipping_method_for_authenticated_users(): void
    {
        $token = $this->logInAsAdminAndGetToken();

        $this->client->request(
            'DELETE',
            $this->getShippingMethodUrl($this->loadFixturesAndGetShippingMethod('ups')),
            [],
            [],
            $this->getAuthorizedHeaderWithAccept($token)
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    private function getAuthorizedHeaderWithAccept(string $token): array
    {
        return [
            'HTTP_Authorization' => 'Bearer ' . $token,
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }

    private function getShippingMethodUrl(?ShippingMethodInterface $shippingMethod = null): string
    {
        $url = '/new-api/shipping-methods';

        return $shippingMethod !== null ? sprintf('%s/%s', $url, $shippingMethod->getCode()) : $url;
    }

    private function loadFixturesAndGetShippingMethod(string $shippingMethodName): ShippingMethodInterface
    {
        $shippingMethods = $this->loadFixturesFromFiles([
            'resources/zones.yml',
            'resources/shipping_methods.yml',
        ]);

        return $shippingMethods[$shippingMethodName];
    }

    private function logInAsAdminAndGetToken(): string
    {
        /** @var AdminUserInterface $admin */
        $admin = $this->loadFixturesFromFile('authentication/api.yml')['admin'];

        $this->client->request(
            'POST',
            '/new-api/admin-user-authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode(['email' => $admin->getEmail(), 'password' => 'sylius-api'])
        );

        return json_decode($this->client->getResponse()->getContent(), true)['token'];
    }
}
