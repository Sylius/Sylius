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
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxonImagesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_taxon_images(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/taxon-images', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_images_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_taxon_images_with_an_image_filter(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/taxon-images',
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_images_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_taxon_images_with_an_invalid_image_filter(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/taxon-images',
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_gets_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxon-images/%s', $taxonImage->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_image_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_a_taxon_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxon-images/%s', $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_image_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_a_taxon_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxon-images/%s', $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_gets_taxon_images_for_the_given_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_images_for_given_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_taxon_images_for_the_given_taxon_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_images_for_given_taxon_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_prevents_getting_taxon_images_for_the_given_taxon_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_creates_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/post_taxon_image_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_taxon_image_with_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            parameters: ['type' => 'banner'],
            files: ['file' => $this->getUploadedFile('fixtures/mugs.jpg', 'mugs.jpg')],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/post_taxon_image_with_type_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_only_the_type_of_the_existing_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon_mug'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/taxon-images/%s', $taxonImage->getId()),
            server: $header,
            content: json_encode([
                'type' => 'logo',
                'owner' => sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
                'path' => 'logo.jpg',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/put_taxon_image_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/taxon-images/%s', $taxonImage->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
