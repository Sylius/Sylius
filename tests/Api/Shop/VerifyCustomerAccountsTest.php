<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Core\Test\Services\EmailChecker;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;

final class VerifyCustomerAccountsTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_resends_account_verification_token(): void
    {
        $this->markTestSkipped('EmailChecker fixed required');

        $container = self::$kernel->getContainer();

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('filesystem.public');

        /** @var EmailChecker $emailChecker */
        $emailChecker = $container->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'authentication/customer.yaml']);
        $token = $this->logInShopUser('oliver@doe.com');

        $this->client->request(
            'POST',
            '/api/v2/shop/account-verification-requests',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token)
            ],
            '{}'
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
        self::assertSame(1, $emailChecker->countMessagesTo('oliver@doe.com'));
    }

    /** @test */
    public function it_does_not_allow_to_resend_token_for_not_logged_in_users(): void
    {
        $container = self::bootKernel()->getContainer();

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('filesystem.public');

        /** @var EmailChecker $emailChecker */
        $emailChecker = $container->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'authentication/customer.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/account-verification-requests',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            '{}'
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
        self::assertFalse($filesystem->exists($emailChecker->getSpoolDirectory()));
    }
}
