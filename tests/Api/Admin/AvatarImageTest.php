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

namespace Api\Admin;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class AvatarImageTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_an_avatar_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'avatar_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AvatarImageInterface $avatarImage */
        $avatarImage = $fixtures['avatar_image'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/avatar-images/%s', $avatarImage->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/avatar_image/get_an_avatar_image',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_nothing_if_avatar_image_not_found(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'avatar_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/avatar-images/%s', 'wrong input'),
            server: $header,
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_an_avatar_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'avatar_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AdminUserInterface $adminUser */
        $adminUser = $fixtures['admin'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/avatar-images',
            parameters: [
                'owner' => sprintf('/api/v2/admin/administrators/%s', $adminUser->getId()),
            ],
            files: ['file' => $this->getUploadedFile('fixtures/ford.jpg', 'ford.jpg')],
            server: $header,
        );

        $response = $this->client->getResponse();
        $this->assertResponse(
            $response,
            'admin/avatar_image/post_avatar_image',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_deletes_an_avatar_image(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'avatar_image.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AvatarImageInterface $avatarImage */
        $avatarImage = $fixtures['avatar_image'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/avatar-images/%s', $avatarImage->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
