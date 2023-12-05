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

use Doctrine\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\AbstractResourceOwner;
use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;
use PHPUnit\Framework\Assert;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;

final class UpdatingUserPasswordEncoderTest extends AbstractWebTestCase
{
    /** @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createClient(['test_case' => 'PasswordHasherState']);
        $this->client->followRedirects();

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
            PurgeMode::createDeleteMode(),
        );
    }

    /** @test */
    public function it_updates_the_encoder_when_the_shop_user_logs_in(): void
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->client->getContainer()->get('sylius.repository.shop_user');

        /** @var ObjectManager $shopUserManager */
        $shopUserManager = $this->client->getContainer()->get('sylius.manager.shop_user');

        $shopUser = $shopUserRepository->findOneByEmail('oliver@doe.com');

        Assert::assertNotNull($shopUser, 'Could not find Shop User with oliver@doe.com email address');

        $passwordHasher = new SodiumPasswordHasher();
        $shopUser->setPassword($passwordHasher->hash('testpassword'));
        $shopUser->setEncoderName('sodium');

        $shopUserManager->persist($shopUser);
        $shopUserManager->flush();

        $this->client->request('GET', '/en_US/login');

        $this->submitForm('Login', [
            '_username' => 'Oliver@doe.com',
            '_password' => 'testpassword',
        ]);

        Assert::assertSame(200, $this->client->getResponse()->getStatusCode());
        Assert::assertSame('/en_US/', parse_url($this->client->getCrawler()->getUri(), \PHP_URL_PATH));
        Assert::assertSame('argon2i', $shopUserRepository->findOneByEmail('oliver@doe.com')->getEncoderName());
    }

    /** @test */
    public function it_updates_the_encoder_when_the_admin_user_logs_in(): void
    {
        /** @var UserRepositoryInterface $adminUserRepository */
        $adminUserRepository = $this->client->getContainer()->get('sylius.repository.admin_user');

        /** @var ObjectManager $adminUserManager */
        $adminUserManager = $this->client->getContainer()->get('sylius.manager.admin_user');

        $adminUser = $adminUserRepository->findOneByEmail('user@example.com');

        $passwordHasher = new SodiumPasswordHasher();
        $adminUser->setPassword($passwordHasher->hash('testpassword'));
        $adminUser->setEncoderName('sodium');

        $adminUserManager->persist($adminUser);
        $adminUserManager->flush();

        $this->client->request('GET', '/admin/login');

        $this->submitForm('Login', [
            '_username' => 'user@example.com',
            '_password' => 'testpassword',
        ]);

        Assert::assertSame(200, $this->client->getResponse()->getStatusCode());
        Assert::assertSame('/admin/', parse_url($this->client->getCrawler()->getUri(), \PHP_URL_PATH));
        Assert::assertSame('argon2i', $adminUserRepository->findOneByEmail('user@example.com')->getEncoderName());
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
            ],
        );

        $responseMock = $this->createConfiguredMock(
            AbstractUserResponse::class,
            [
                'getUsername' => 'someUserName',
                'getResourceOwner' => $resourceOwnerMock,
                'getAccessToken' => 'LongAccessToken',
                'getRefreshToken' => 'LongRefreshToken',
            ],
        );

        $oAuthUserProvider->connect($shopUser, $responseMock);

        Assert::assertSame($initialOAuthAccounts + 1, $shopUser->getOAuthAccounts()->count());
    }

    private function submitForm(string $button, array $fieldValues = []): void
    {
        $buttonNode = $this->client->getCrawler()->selectButton($button);

        $form = $buttonNode->form($fieldValues);

        $this->client->submit($form);
    }
}
