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
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductImagesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_denies_access_to_a_product_images_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFile('product/product_image.yaml');

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-images');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_images(): void
    {
        $this->loadFixturesFromFiles(['product/product_image.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/authentication-token',
            server: $header,
            content: json_encode(['email' => 'api@example.com', 'password' => 'sylius'], JSON_THROW_ON_ERROR),
        );

        $this->client->request(
            method: 'GET',
            uri: 'product-images',
            server: $header,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_images_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_one_product_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_image.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductImageInterface $productImage */
        $productImage = $fixtures['product_thumbnail'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/authentication-token',
            server: $header,
            content: json_encode(['email' => 'api@example.com', 'password' => 'sylius'], JSON_THROW_ON_ERROR),
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('product-images/%s', $productImage->getId()),
            server: $header,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_image_response', Response::HTTP_OK);
    }
}
