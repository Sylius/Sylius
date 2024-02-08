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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductVariantsTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_a_products_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml']);

        $this->client->request('GET', '/api/v2/admin/product-variants/MUG');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_all_product_variants(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => 'api@example.com', 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        $this->client->request(
            'GET',
            'product-variants/MUG',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER)
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_product_variants_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_updates_channel_pricing_of_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => 'api@example.com', 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $fixtures['product_variant'];
        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER),
            json_encode([
                'channelPricings' => ['WEB' => [
                    '@id' => sprintf('/api/v2/admin/channel-pricings/%s', $productVariant->getChannelPricingForChannel($channel)->getId()),
                    'price' => 3000,
                    'originalPrice' => 4000,
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/put_product_variant_response',
            Response::HTTP_OK
        );
    }

    /** @test */
    public function it_creates_product_variant(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => 'api@example.com', 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        /** @var ProductInterface $product */
        $product = $fixtures['product'];

        $this->client->request(
            'POST',
            '/api/v2/admin/product-variants',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER),
            json_encode([
                'code' => 'MUG_2',
                'position' => 1,
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'channelPricings' => ['WEB' => [
                    'channelCode' => 'WEB',
                    'price' => 4000,
                    'originalPrice' => 5000,
                    'minimumPrice' => 2000,
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_product_variant_response',
            Response::HTTP_CREATED
        );
    }
}
