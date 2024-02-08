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
            ['Tests/Application/config/fixtures/administrator.yaml', 'Tests/Application/config/fixtures/channel.yaml'],
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

        putenv('SYLIUS_API_ENABLED=true');
    }
}
