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

namespace Sylius\Tests\Api\Shop;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ResendVerificationEmailTest extends JsonApiTestCase
{
    #[Test]
    public function it_sends_verification_email_for_existing_unverified_user(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'authentication/shop_user.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/verification-request',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode(['email' => 'oliver@doe.com', 'localeCode' => 'en_US']),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        self::assertEmailCount(1);
        self::assertEmailAddressContains(self::getMailerMessage(), 'To', 'oliver@doe.com');
    }

    #[Test]
    public function it_returns_accepted_without_sending_email_for_non_existing_user(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'authentication/shop_user.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/verification-request',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode(['email' => 'notexisting@example.com', 'localeCode' => 'en_US']),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        self::assertEmailCount(0);
    }

    #[Test]
    public function it_returns_accepted_without_sending_email_for_already_verified_user(): void
    {
        $data = $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'authentication/shop_user.yaml']);

        /** @var ShopUserInterface $shopUser */
        $shopUser = $data['shop_user_oliver'];
        $shopUser->setVerifiedAt(new \DateTime());
        $this->getEntityManager()->flush();

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/verification-request',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode(['email' => 'oliver@doe.com', 'localeCode' => 'en_US']),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        self::assertEmailCount(0);
    }

    #[Test]
    public function it_does_not_require_authentication(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'authentication/shop_user.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers/verification-request',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode(['email' => 'oliver@doe.com', 'localeCode' => 'en_US']),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
    }
}
