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

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CatalogPromotionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_allows_admin_to_get_catalog_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/catalog-promotions'),
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_catalog_promotions_response', Response::HTTP_OK);
    }
}
