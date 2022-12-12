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

namespace Sylius\Tests\Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\AbstractResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

final class UpdatingUserPasswordEncoderTest extends WebTestCase
{
    /** @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects(true);

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = $this->client->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load(
            [
                __DIR__ . '/../DataFixtures/ORM/resources/channels.yml',
                __DIR__ . '/../DataFixtures/ORM/resources/customers.yml',
                __DIR__ . '/../DataFixtures/ORM/resources/admin_users.yml',
            ],
            [],
            [],
            PurgeMode::createDeleteMode()
        );
    }

    /** @test */
    public function oauth_user_factory_is_not_overridden(): void
    {
        if (!$this->client->getContainer()->has('sylius.oauth.user_provider')) {
            $this->markTestSkipped('HWIOAuthBundle not installed');

            return;
        }

        $oAuthUserProvider = $this->client->getContainer()->get('sylius.oauth.user_provider');
        $shopUserRepository = $this->client->getContainer()->get('sylius.repository.shop_user');
        $shopUser = $shopUserRepository->findOneByEmail('Oliver@doe.com');
        $initialOAuthAccounts = $shopUser->getOAuthAccounts()->count();

        $resourceOwnerMock = $this->createConfiguredMock(
            AbstractResourceOwner::class,
            [
                'getName' => 'resourceProviderName',
            ]
        );

        $responseMock = $this->createConfiguredMock(
            AbstractUserResponse::class,
            [
                'getUsername' => 'someUserName',
                'getResourceOwner' => $resourceOwnerMock,
                'getAccessToken' => 'LongAccessToken',
                'getRefreshToken' => 'LongRefreshToken',
            ]
        );

        $oAuthUserProvider->connect($shopUser, $responseMock);

        Assert::assertSame($initialOAuthAccounts + 1, $shopUser->getOAuthAccounts()->count());
    }
}
