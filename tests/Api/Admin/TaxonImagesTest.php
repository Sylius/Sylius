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

use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxonImagesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    #[Test]
    public function it_gets_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_image_response',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_gets_a_taxon_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon_image/get_taxon_image_with_image_filter_response',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_prevents_getting_a_taxon_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_creates_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::FILE_CONTENT_TYPE_HEADER);

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

    #[Test]
    public function it_creates_a_taxon_image_with_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::FILE_CONTENT_TYPE_HEADER);

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

    #[Test]
    public function it_prevents_taxon_image_creation_with_a_disallowed_file_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::FILE_CONTENT_TYPE_HEADER);

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon'];

        $storedImagesCount = $this->countStoredImages();

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/taxons/%s/images', $taxon->getCode()),
            files: ['file' => $this->getUploadedFile('xss.svg', 'xss.svg', 'image/svg+xml')],
            server: $header,
        );

        $this->assertResponseViolations(
            [
                [
                    'propertyPath' => 'file',
                    'message' => 'The file is not a valid image. Allowed types are image/jpeg, image/png, image/gif, image/webp.',
                ],
            ],
        );

        $this->assertSame($storedImagesCount, $this->countStoredImages());
    }

    #[Test]
    public function it_updates_only_the_type_of_the_existing_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
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

    #[Test]
    public function it_deletes_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxon_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    private function countStoredImages(): int
    {
        $imagesDirectory = self::getContainer()->getParameter('sylius_core.images_dir');
        if (!is_dir($imagesDirectory)) {
            return 0;
        }

        return iterator_count(new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($imagesDirectory, \FilesystemIterator::SKIP_DOTS),
        ));
    }
}
