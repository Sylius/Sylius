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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\Handler\ResourceDeleteHandler;
use Sylius\Bundle\ResourceBundle\Controller\ResourceDeleteHandlerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Exception\DeleteHandlingException;
use Sylius\Resource\Model\ResourceInterface;

final class ResourceDeleteHandlerTest extends TestCase
{
    private MockObject&ResourceDeleteHandlerInterface $decoratedHandler;

    private EntityManagerInterface&MockObject $entityManager;

    private ResourceDeleteHandler $resourceDeleteHandler;

    protected function setUp(): void
    {
        $this->decoratedHandler = $this->createMock(ResourceDeleteHandlerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->resourceDeleteHandler = new ResourceDeleteHandler($this->decoratedHandler, $this->entityManager);
    }

    public function testItImplementsResourceDeleteHandlerInterface(): void
    {
        $this->assertInstanceOf(ResourceDeleteHandlerInterface::class, $this->resourceDeleteHandler);
    }

    public function testItUsesDecoratedHandlerToHandleResourceDeletion(): void
    {
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);

        $this->entityManager->expects(self::once())->method('beginTransaction');
        $this->decoratedHandler->expects(self::once())->method('handle')->with($resource, $repository);
        $this->entityManager->expects(self::once())->method('commit');

        $this->resourceDeleteHandler->handle($resource, $repository);
    }

    public function testItThrowsDeleteHandlingExceptionIfForeignKeyConstraintViolationExceptionOccurs(): void
    {
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);
        $exception = $this->createMock(ForeignKeyConstraintViolationException::class);

        $this->entityManager->expects(self::once())->method('beginTransaction');

        $this->decoratedHandler
            ->expects(self::once())
            ->method('handle')
            ->with($resource, $repository)
            ->willThrowException($exception)
        ;

        $this->entityManager->expects(self::once())->method('rollback');
        $this->entityManager->expects(self::never())->method('commit');

        $this->expectException(DeleteHandlingException::class);

        $this->resourceDeleteHandler->handle($resource, $repository);
    }

    public function testItThrowsDeleteHandlingExceptionIfSomethingWentWrongWithOrm(): void
    {
        $repository = $this->createMock(RepositoryInterface::class);
        $resource = $this->createMock(ResourceInterface::class);

        $this->entityManager->expects(self::once())->method('beginTransaction');

        $this->decoratedHandler
            ->expects(self::once())
            ->method('handle')
            ->with($resource, $repository)
            ->willThrowException(new EntityNotFoundException('ORM error'))
        ;

        $this->entityManager->expects(self::once())->method('rollback');
        $this->entityManager->expects(self::never())->method('commit');

        $this->expectException(DeleteHandlingException::class);

        $this->resourceDeleteHandler->handle($resource, $repository);
    }
}
