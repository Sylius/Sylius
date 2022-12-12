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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductTaxonTest extends JsonApiTestCase
{
    /** @test */
    public function it_creates_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['taxonomy.yaml', 'product/product_variant.yaml', 'authentication/api_administrator.yaml']);

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

        $taxon = $fixtures['hat_taxon'];

        $this->client->request(
            'POST',
            '/api/v2/admin/product-taxons',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER),
            json_encode([
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'taxon' => sprintf('/api/v2/admin/taxons/%s', $taxon->getCode())
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_product_taxon_response',
            Response::HTTP_CREATED
        );
    }
}
