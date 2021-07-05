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

use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductImageTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_a_product_images_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFile('product_image.yaml');

        $this->client->request('GET', '/api/v2/admin/product-images');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_images(): void
    {
        $this->loadFixturesFromFiles(['product_image.yaml', 'authentication/api_administrator.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => 'api@example.com', 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        $this->client->request(
            'GET',
            'product-images',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER)
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_images_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_one_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product_image.yaml', 'authentication/api_administrator.yaml']);
        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures["product_thumbnail"];

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => 'api@example.com', 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        $this->client->request(
            'GET',
            sprintf('product-images/%s', (string) $productImage->getId()),
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER)
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_image_response', Response::HTTP_OK);
    }
}
