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

use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxCategoriesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_tax_category(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_category.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxCategoryInterface $taxCategory */
        $taxCategory = $fixtures['tax_category_special'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/tax-categories/%s', $taxCategory->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_category/get_tax_category_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_tax_categories(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_category.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/tax-categories', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_category/get_tax_categories_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_tax_category(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/tax-categories',
            server: $header,
            content: json_encode([
                'code' => 'ultra',
                'name' => 'Ultra',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_category/post_tax_category_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_tax_category(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_category.yaml']);

        /** @var TaxCategoryInterface $taxCategory */
        $taxCategory = $fixtures['tax_category_default'];

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/tax-categories/' . $taxCategory->getCode(),
            server: $header,
            content: json_encode([
                'name' => 'Not so default',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_category/put_tax_category_response',
            Response::HTTP_OK,
        );
    }
}
