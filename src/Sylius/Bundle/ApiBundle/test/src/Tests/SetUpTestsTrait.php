<?php

declare(strict_types=1);

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

trait SetUpTestsTrait
{
    /** @var string */
    private $JWTAdminUserToken;

    /** @var array */
    private $fixturesFiles;

    /** @var array */
    private $objects;

    public function setFixturesFiles(array $fixturesFiles): void
    {
        $this->fixturesFiles = array_merge(
            $fixturesFiles,
            ['test/config/fixtures/administrator.yaml', 'test/config/fixtures/channel.yaml']
        );
    }

    public function setUpTest(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        /** @var LoaderInterface $loader */
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');

        /** @var JWTTokenManagerInterface $JWTManager */
        $JWTManager = $container->get('lexik_jwt_authentication.jwt_manager');

        $this->objects = $loader->load($this->fixturesFiles, [], [], PurgeMode::createDeleteMode());

        $adminUser = $this->objects['admin'];

        $this->JWTAdminUserToken = $JWTManager->create($adminUser);

        $_ENV['SYLIUS_API_ENABLED'] = true;
    }
}
