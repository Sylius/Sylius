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
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SequentialOrderNumberGeneratorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private OrderNumberGeneratorInterface $numberGenerator;

    /** Plays the role of a concurrent checkout. */
    private Connection $concurrentConnection;

    protected function setUp(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->numberGenerator = self::getContainer()->get('sylius.number_generator.sequential_order');

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM sylius_order_sequence');
        $connection->executeStatement('INSERT INTO sylius_order_sequence (id, idx, version) VALUES (1, 0, 1)');

        $this->concurrentConnection = DriverManager::getConnection($connection->getParams());
    }

    protected function tearDown(): void
    {
        $this->concurrentConnection->close();

        parent::tearDown();
    }

    #[Test]
    public function it_uses_the_index_committed_by_a_concurrent_checkout_after_the_sequence_was_loaded(): void
    {
        $this->entityManager->beginTransaction();

        try {
            self::getContainer()->get('sylius.repository.order_sequence')->findOneBy([]);
            $this->concurrentConnection->executeStatement('UPDATE sylius_order_sequence SET idx = idx + 1');

            $this->assertSame('000000002', $this->numberGenerator->generate(new Order()));
        } finally {
            $this->entityManager->rollback();
        }
    }

    #[Test]
    public function it_keeps_the_sequence_locked_until_the_transaction_ends(): void
    {
        $this->entityManager->beginTransaction();

        try {
            $this->numberGenerator->generate(new Order());

            $this->expectException(DriverException::class);

            $this->concurrentConnection->executeQuery('SELECT idx FROM sylius_order_sequence FOR UPDATE NOWAIT');
        } finally {
            $this->entityManager->rollback();
        }
    }
}
