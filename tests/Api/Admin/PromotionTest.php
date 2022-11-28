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

use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PromotionTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion.yaml']);
        $header = $this->getLoggedHeader();

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/get_promotion_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/promotions',
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/get_promotions_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_promotion(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/promotions',
            [],
            [],
            $header,
            json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'appliesToDiscounted' => false,
                'translations' => ['en_US' => [
                    'locale' => 'en_US',
                    'label' => 'T-Shirts discount',
                ]],

            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/post_promotion_response',
            Response::HTTP_CREATED,
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
