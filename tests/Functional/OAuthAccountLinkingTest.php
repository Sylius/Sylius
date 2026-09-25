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

namespace Sylius\Tests\Functional;

use ApiTestCase\JsonApiTestCase;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Tests\Functional\Stub\OAuthHttpClientStub;

final class OAuthAccountLinkingTest extends JsonApiTestCase
{
    private const GOOGLE_IDENTIFIER = '108412345678901234567';

    private const SHOP_USER_EMAIL = 'shop@example.com';

    #[Before]
    public function setUpClient(): void
    {
        $this->client = self::createClient(['debug' => false], ['HTTP_ACCEPT' => 'text/html']);
        $this->client->followRedirects();
    }

    #[Test]
    public function it_refuses_to_link_an_oauth_identity_with_an_existing_account_when_the_provider_does_not_confirm_the_email_address(): void
    {
        $this->loadFixtures();
        $this->respondWithUserInformation(verified: false);

        $this->client->request('GET', '/login/check-google?code=an-authorization-code');

        $this->assertStringContainsString(
            'The e-mail address has not been confirmed by google. Please sign in with your e-mail address and password.',
            (string) $this->client->getResponse()->getContent(),
        );
        $this->assertNull($this->findOAuthAccount());
    }

    #[Test]
    public function it_links_an_oauth_identity_with_an_existing_account_when_the_provider_confirms_the_email_address(): void
    {
        $this->loadFixtures();
        $this->respondWithUserInformation(verified: true);

        $this->client->request('GET', '/login/check-google?code=an-authorization-code');

        $this->client->request('GET', '/en_US/account/dashboard');
        $this->assertResponseIsSuccessful();

        $oauthAccount = $this->findOAuthAccount();
        $this->assertInstanceOf(UserOAuthInterface::class, $oauthAccount);

        $user = $oauthAccount->getUser();
        $this->assertInstanceOf(ShopUserInterface::class, $user);
        $this->assertSame(self::SHOP_USER_EMAIL, $user->getEmail());
    }

    private function loadFixtures(): void
    {
        $this->loadFixturesFromFiles([
            'resources/channels.yml',
            'authentication/shop_user.yml',
        ]);
    }

    private function respondWithUserInformation(bool $verified): void
    {
        /** @var OAuthHttpClientStub $httpClient */
        $httpClient = self::getContainer()->get(OAuthHttpClientStub::class);

        $httpClient->willRespondTo('accounts.google.com/o/oauth2/token', [
            'access_token' => 'an-access-token',
            'token_type' => 'Bearer',
        ]);

        $httpClient->willRespondTo('googleapis.com/oauth2/v1/userinfo', [
            'email' => self::SHOP_USER_EMAIL,
            'family_name' => 'Doe',
            'given_name' => 'John',
            'id' => self::GOOGLE_IDENTIFIER,
            'name' => 'John Doe',
            'verified_email' => $verified,
        ]);
    }

    private function findOAuthAccount(): ?UserOAuthInterface
    {
        /** @var ObjectManager $manager */
        $manager = self::getContainer()->get('doctrine')->getManager();
        $manager->clear();

        /** @var RepositoryInterface<UserOAuthInterface> $repository */
        $repository = self::getContainer()->get('sylius.repository.oauth_user');

        return $repository->findOneBy(['provider' => 'google', 'identifier' => self::GOOGLE_IDENTIFIER]);
    }
}
