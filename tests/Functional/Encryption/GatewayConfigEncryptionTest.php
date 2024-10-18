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

namespace Sylius\Tests\Functional\Encryption;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\PaymentBundle\Listener\GatewayConfigEncryptionListener;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class GatewayConfigEncryptionTest extends KernelTestCase
{
    private static array $gatewayConfigData = [
        'api' => [
            'pk' => 'test',
            'sk' => 'test',
            'url' => 'https://example.com',
        ],
        'signature' => 'test',
        'merchant_id' => 'test',
    ];

    private EntityManagerInterface $entityManager;

    /** @var RepositoryInterface<GatewayConfigInterface> */
    private RepositoryInterface $gatewayConfigRepository;

    /** @var FactoryInterface<GatewayConfigInterface> */
    private FactoryInterface $gatewayFactory;

    public function setUp(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $this->gatewayConfigRepository = self::getContainer()->get('sylius.repository.gateway_config');
        $this->gatewayFactory = self::getContainer()->get('sylius.factory.gateway_config');

        $encrypter = self::getContainer()->get('sylius.encrypter.gateway_config');
        self::getContainer()->set('sylius.listener.gateway_config_encryption', new GatewayConfigEncryptionListener(
            $encrypter,
            'Sylius\Bundle\PayumBundle\Model\GatewayConfig',
            ['online-disabled'],
        ));

        $this->loadFixtures([
            __DIR__ . '/../../DataFixtures/ORM/resources/channels.yml',
        ]);
    }

    /** @test */
    public function it_covers_encryption_and_decryption_when_saving_and_loading(): void
    {
        $gatewayConfig = $this->gatewayFactory->createNew();
        $gatewayConfig->setGatewayName('Online');
        $gatewayConfig->setFactoryName('online');
        $gatewayConfig->setConfig(self::$gatewayConfigData);

        $this->gatewayConfigRepository->add($gatewayConfig);
        self::assertSame(self::$gatewayConfigData, $gatewayConfig->getConfig());

        $this->entityManager->clear();

        $gatewayConfigFromDatabase = $this->getDatabaseConfigDataForGateway('Online');
        self::assertNotSame($gatewayConfig->getConfig(), $gatewayConfigFromDatabase);

        $gatewayFromRepository = $this->gatewayConfigRepository->findOneBy(['gatewayName' => 'Online']);
        self::assertSame($gatewayConfig->getConfig(), $gatewayFromRepository->getConfig());
        self::assertSame(self::$gatewayConfigData, $gatewayConfig->getConfig());

        $gatewayConfigFromDatabase = $this->getDatabaseConfigDataForGateway('Online');
        self::assertNotSame($gatewayConfig->getConfig(), $gatewayConfigFromDatabase);
        self::assertNotSame(self::$gatewayConfigData, $gatewayConfigFromDatabase);
    }

    /** @test */
    public function it_does_not_encrypt_when_gateway_factory_is_disabled_for_encryption(): void
    {
        $gatewayConfig = $this->gatewayFactory->createNew();
        $gatewayConfig->setGatewayName('online_disabled');
        $gatewayConfig->setFactoryName('online-disabled');
        $gatewayConfig->setConfig(self::$gatewayConfigData);

        $this->gatewayConfigRepository->add($gatewayConfig);
        self::assertSame(self::$gatewayConfigData, $gatewayConfig->getConfig());

        $this->entityManager->clear();

        $gatewayConfigFromDbal = $this->getDatabaseConfigDataForGateway('online_disabled');
        self::assertSame($gatewayConfig->getConfig(), $gatewayConfigFromDbal);

        $gatewayFromRepository = $this->gatewayConfigRepository->findOneBy(['gatewayName' => 'online_disabled']);
        self::assertSame($gatewayConfig->getConfig(), $gatewayFromRepository->getConfig());
        self::assertSame(self::$gatewayConfigData, $gatewayConfig->getConfig());

        $gatewayConfigFromDbal = $this->getDatabaseConfigDataForGateway('online_disabled');
        self::assertSame($gatewayConfig->getConfig(), $gatewayConfigFromDbal);
        self::assertSame(self::$gatewayConfigData, $gatewayConfigFromDbal);
    }

    /** @test */
    public function it_does_not_encrypt_empty_config(): void
    {
        $gatewayConfig = $this->gatewayFactory->createNew();
        $gatewayConfig->setGatewayName('Online');
        $gatewayConfig->setFactoryName('online');
        $gatewayConfig->setConfig([]);

        $this->gatewayConfigRepository->add($gatewayConfig);
        self::assertSame([], $gatewayConfig->getConfig());

        $this->entityManager->clear();

        $gatewayConfigFromDbal = $this->getDatabaseConfigDataForGateway('Online');
        self::assertSame([], $gatewayConfigFromDbal);

        $gatewayFromRepository = $this->gatewayConfigRepository->findOneBy(['gatewayName' => 'Online']);
        self::assertSame($gatewayConfig->getConfig(), $gatewayFromRepository->getConfig());
        self::assertSame([], $gatewayConfig->getConfig());

        $gatewayConfigFromDbal = $this->getDatabaseConfigDataForGateway('Online');
        self::assertSame($gatewayConfig->getConfig(), $gatewayConfigFromDbal);
        self::assertSame([], $gatewayConfigFromDbal);
    }

    private function getDatabaseConfigDataForGateway(string $gatewayName): array
    {
        $result = $this->entityManager->getConnection()->executeQuery(
            'SELECT config FROM sylius_gateway_config WHERE gateway_name = :gatewayName',
            ['gatewayName' => $gatewayName],
        );

        return json_decode($result->fetchOne(), true);
    }

    private function loadFixtures(array $fixtureFiles): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load($fixtureFiles, [], [], PurgeMode::createDeleteMode());
    }
}
