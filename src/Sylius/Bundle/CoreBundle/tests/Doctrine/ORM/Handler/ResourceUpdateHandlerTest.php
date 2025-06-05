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

namespace Tests\Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceUpdateHandler;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceUpdateHandlerInterface;
use Sylius\Resource\Exception\RaceConditionException;
use Sylius\Resource\Model\ResourceInterface;

final class ResourceUpdateHandlerTest extends TestCase
{
    private MockObject&ResourceUpdateHandlerInterface $decoratedUpdater;

    private EntityManagerInterface&MockObject $entityManager;

    private ResourceUpdateHandler $resourceUpdateHandler;

    protected function setUp(): void
    {
        $this->decoratedUpdater = $this->createMock(ResourceUpdateHandlerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->resourceUpdateHandler = new ResourceUpdateHandler($this->decoratedUpdater, $this->entityManager);
    }

    public function testImplementsAResourceUpdateHandlerInterface(): void
    {
        $this->assertInstanceOf(ResourceUpdateHandlerInterface::class, $this->resourceUpdateHandler);
    }

    public function testUsesDecoratedUpdaterToHandleUpdate(): void
    {
        $resource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $manager = $this->createMock(ObjectManager::class);

        $this->entityManager->expects($this->once())->method('beginTransaction');

        $this->decoratedUpdater
            ->expects($this->once())
            ->method('handle')
            ->with($resource, $configuration, $manager)
        ;

        $this->entityManager->expects($this->once())->method('commit');
        $this->entityManager->expects($this->never())->method('rollback');

        $this->resourceUpdateHandler->handle($resource, $configuration, $manager);
    }

    public function testThrowsARaceConditionExceptionIfCatchAnOptimisticLockException(): void
    {
        $resource = $this->createMock(ResourceInterface::class);
        $configuration = $this->createMock(RequestConfiguration::class);
        $manager = $this->createMock(ObjectManager::class);

        $this->entityManager->expects($this->once())->method('beginTransaction');

        $this->decoratedUpdater
            ->expects($this->once())
            ->method('handle')
            ->with($resource, $configuration, $manager)
            ->willThrowException($this->createMock(OptimisticLockException::class))
        ;

        $this->entityManager->expects($this->never())->method('commit');
        $this->entityManager->expects($this->once())->method('rollback');

        $this->expectException(RaceConditionException::class);

        $this->resourceUpdateHandler->handle($resource, $configuration, $manager);
    }
}
