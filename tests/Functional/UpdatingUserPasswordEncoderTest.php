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

use Doctrine\Common\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use HWI\Bundle\OAuthBundle\Tests\Fixtures\CustomUserResponse;
use PHPUnit\Framework\Assert;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Repository\UserRepositoryInterface;
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
    public function it_updates_the_encoder_when_the_shop_user_logs_in(): void
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->client->getContainer()->get('sylius.repository.shop_user');

        /** @var ObjectManager $shopUserManager */
        $shopUserManager = $this->client->getContainer()->get('sylius.manager.shop_user');

        $shopUser = $shopUserRepository->findOneByEmail('Oliver@doe.com');
        $shopUser->setPlainPassword('testpassword');
        $shopUser->setEncoderName('sha512');

        $shopUserManager->persist($shopUser);
        $shopUserManager->flush();

        $this->client->request('GET', '/en_US/login');

        $this->submitForm('Login', [
            '_username' => 'Oliver@doe.com',
            '_password' => 'testpassword',
        ]);

        Assert::assertSame(200, $this->client->getResponse()->getStatusCode());
        Assert::assertSame('/en_US/', parse_url($this->client->getCrawler()->getUri(), \PHP_URL_PATH));
        Assert::assertSame('argon2i', $shopUserRepository->findOneByEmail('Oliver@doe.com')->getEncoderName());
    }

    /** @test */
    public function it_updates_the_encoder_when_the_admin_user_logs_in(): void
    {
        /** @var UserRepositoryInterface $adminUserRepository */
        $adminUserRepository = $this->client->getContainer()->get('sylius.repository.admin_user');

        /** @var ObjectManager $adminUserManager */
        $adminUserManager = $this->client->getContainer()->get('sylius.manager.admin_user');

        $adminUser = $adminUserRepository->findOneByEmail('user@example.com');
        $adminUser->setPlainPassword('testpassword');
        $adminUser->setEncoderName('sha512');

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
        $oAuthUserProvider = $this->client->getContainer()->get('sylius.oauth.user_provider');
        $shopUserRepository = $this->client->getContainer()->get('sylius.repository.shop_user');
        $shopUser = $shopUserRepository->findOneByEmail('Oliver@doe.com');

        $response = new CustomUserResponse();
        $oAuthUserProvider->connect($shopUser, $response);
    }

    private function submitForm(string $button, array $fieldValues = []): void
    {
        $buttonNode = $this->client->getCrawler()->selectButton($button);

        $form = $buttonNode->form($fieldValues);

        $this->client->submit($form);
    }
}
