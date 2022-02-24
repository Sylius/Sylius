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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductVariantsCacheTest extends JsonApiTestCase
{
    /** @test */
    public function it_updates_channel_pricing_of_product_variant_in_both_admin_and_shop(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);

        $header = $this->authorizeAdministrator();

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

        $this->assertResponse($this->client->getResponse(), 'admin/put_product_variant_response', Response::HTTP_OK);

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/product-variants/%s', $productVariant->getCode()),
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER)
        );

        $this->assertResponse($this->client->getResponse(), 'admin/get_product_variant_after_update_response', Response::HTTP_OK);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/product-variants/%s', $productVariant->getCode()),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );

        $this->assertResponse($this->client->getResponse(), 'shop/product/get_product_variant_after_update_response', Response::HTTP_OK);
    }

    private function authorizeAdministrator(): array
    {
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

        return $header;
    }
}
