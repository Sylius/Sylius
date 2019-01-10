<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Doctrine\Common\Persistence\ObjectManager;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Assert;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UpdatingUserPasswordEncoderTest extends WebTestCase
{
    protected function setUp(): void
    {
        static::bootKernel();

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');

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
        $client = static::createClient();
        $client->followRedirects(true);

        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = static::$container->get('sylius.repository.shop_user');

        /** @var ObjectManager $shopUserManager */
        $shopUserManager = static::$container->get('sylius.manager.shop_user');

        $shopUser = $shopUserRepository->findOneByEmail('Oliver@doe.com');
        $shopUser->setPlainPassword('testpassword');
        $shopUser->setEncoderName('sha512');

        $shopUserManager->persist($shopUser);
        $shopUserManager->flush();

        $client->request('GET', '/en_US/login');

        $client->submitForm('Login', [
            '_username' => 'Oliver@doe.com',
            '_password' => 'testpassword'
        ]);

        Assert::assertSame(200, $client->getResponse()->getStatusCode());
        Assert::assertSame('/en_US/', parse_url($client->getCrawler()->getUri(), \PHP_URL_PATH));
        Assert::assertSame('argon2i', $shopUserRepository->findOneByEmail('Oliver@doe.com')->getEncoderName());
    }

    /** @test */
    public function it_updates_the_encoder_when_the_admin_user_logs_in(): void
    {
        $client = static::createClient();
        $client->followRedirects(true);

        /** @var UserRepositoryInterface $adminUserRepository */
        $adminUserRepository = static::$container->get('sylius.repository.admin_user');

        /** @var ObjectManager $adminUserManager */
        $adminUserManager = static::$container->get('sylius.manager.admin_user');

        $adminUser = $adminUserRepository->findOneByEmail('user@example.com');
        $adminUser->setPlainPassword('testpassword');
        $adminUser->setEncoderName('sha512');

        $adminUserManager->persist($adminUser);
        $adminUserManager->flush();

        $client->request('GET', '/admin/login');

        $client->submitForm('Login', [
            '_username' => 'user@example.com',
            '_password' => 'testpassword'
        ]);

        Assert::assertSame(200, $client->getResponse()->getStatusCode());
        Assert::assertSame('/admin/', parse_url($client->getCrawler()->getUri(), \PHP_URL_PATH));
        Assert::assertSame('argon2i', $adminUserRepository->findOneByEmail('user@example.com')->getEncoderName());
    }
}
