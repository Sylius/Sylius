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

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxonsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['category_taxon'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_taxons(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/taxons', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxons_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_taxon(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/taxons',
            server: $header,
            content: json_encode([
                'code' => 'WATCHES',
                'translations' => [
                    'en_US' => [
                        'name' => 'Watches',
                        'slug' => 'watches',
                        'locale' => 'en_US'
                    ]
                ]
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/post_taxon_response',
            Response::HTTP_CREATED,
        );
    }
}
