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

use Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer;
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
    public function it_gets_a_taxon_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['category_taxon'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxon_with_image_filter_response',
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
    public function it_gets_taxons_with_an_image_filter(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/taxons',
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxons_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_taxon(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/taxons',
            server: $header,
            content: json_encode([
                'code' => 'WATCHES',
                'parent' => '/api/v2/admin/taxons/CATEGORY',
                'translations' => [
                    'en_US' => [
                        'name' => 'Watches',
                        'slug' => 'watches',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/post_taxon_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_a_taxon_without_required_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/taxons',
            server: $header,
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/post_taxon_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_create_a_taxon_with_taken_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/taxons',
            server: $header,
            content: json_encode([
                'code' => 'MUG',
                'parent' => '/api/v2/admin/taxons/CATEGORY',
                'translations' => [
                    'en_US' => [
                        'name' => 'Watches',
                        'slug' => 'watches',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/post_taxon_with_taken_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_the_existing_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['mug_taxon'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            server: $header,
            content: json_encode([
                'parent' => '/api/v2/admin/taxons/BRAND',
                'translations' => [
                    'en_US' => [
                        '@id' => sprintf('/api/v2/admin/taxon-translations/%s', $taxon->getTranslation('en_US')->getId()),
                        'name' => 'Watches',
                        'slug' => 'watches',
                    ],
                ],
                'enabled' => false,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/put_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_update_a_taxon_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['mug_taxon'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        'name' => 'Watches',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/put_taxon_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_deletes_a_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['mug_taxon'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_tries_to_delete_a_menu_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['category_taxon'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_CONFLICT);
    }
}
