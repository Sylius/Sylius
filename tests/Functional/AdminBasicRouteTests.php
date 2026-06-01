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

namespace Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Tests\Functional\AbstractWebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class AdminBasicRouteTests extends AbstractWebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testCreate(): void
    {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/basic/create');

        $this->assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/basic/update');

        $this->assertResponseIsSuccessful();
    }

    public function testIndex(): void
    {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/basic/index');

        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/basic/show');

        $this->assertResponseIsSuccessful();
    }

    private function loginAdminUser(): void
    {
        $this->loadFixtures();

        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Login', [
            '_username' => 'sylius',
            '_password' => 'sylius',
        ]);
        $afterLoginContent = $this->client->getResponse()->getContent();

        $this->assertStringNotContainsString('Invalid credentials', $afterLoginContent);
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtureLoader->load([
            __DIR__ . '/../DataFixtures/ORM/authentication/administrator.yml',
            __DIR__ . '/../DataFixtures/ORM/resources/channels.yml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
