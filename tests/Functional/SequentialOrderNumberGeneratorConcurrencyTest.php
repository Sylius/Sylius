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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SequentialOrderNumberGeneratorConcurrencyTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private OrderNumberGeneratorInterface $numberGenerator;

    /** @var RepositoryInterface<object> */
    private RepositoryInterface $sequenceRepository;

    private Connection $concurrentConnection;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->numberGenerator = self::getContainer()->get('sylius.number_generator.sequential_order');
        $this->sequenceRepository = self::getContainer()->get('sylius.repository.order_sequence');

        $this->concurrentConnection = DriverManager::getConnection($this->entityManager->getConnection()->getParams());

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM sylius_order_sequence');
        $connection->executeStatement('INSERT INTO sylius_order_sequence (idx, version) VALUES (0, 1)');
    }

    protected function tearDown(): void
    {
        $this->concurrentConnection->close();

        parent::tearDown();
    }

    #[Test]
    public function it_does_not_reuse_a_number_when_a_concurrent_order_advanced_the_sequence_after_it_was_loaded(): void
    {
        $generatedNumber = null;

        $this->entityManager->wrapInTransaction(function () use (&$generatedNumber): void {
            $this->sequenceRepository->findOneBy([]);

            $this->concurrentConnection->executeStatement('UPDATE sylius_order_sequence SET idx = idx + 1');

            $generatedNumber = $this->numberGenerator->generate($this->createMock(OrderInterface::class));
        });

        self::assertSame('000000002', $generatedNumber);
    }
}
