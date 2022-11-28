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

use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ShippingCategoriesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_shipping_category(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'shipping_category.yaml']);
        $header = $this->getLoggedHeader();

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $fixtures['shipping_category_special'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/shipping-categories/%s', $shippingCategory->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_category/get_shipping_category_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_shipping_categories(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'shipping_category.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/shipping-categories',
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_category/get_shipping_categories_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_shipping_category(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/shipping-categories',
            [],
            [],
            $header,
            json_encode([
                'code' => 'ultra',
                'name' => 'Ultra',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_category/post_shipping_category_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_shipping_category(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'shipping_category.yaml']);

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $fixtures['shipping_category_default'];

        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            '/api/v2/admin/shipping-categories/' . $shippingCategory->getCode(),
            [],
            [],
            $header,
            json_encode([
                'name' => 'Not so default',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_category/put_shipping_category_response',
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
