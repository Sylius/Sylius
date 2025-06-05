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

namespace Tests\Sylius\Bundle\ApiBundle\Resolver;

use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolver;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UriTemplateParentResourceResolverTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;

    private UriTemplateParentResourceResolver $uriTemplateParentResourceResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->uriTemplateParentResourceResolver = new UriTemplateParentResourceResolver($this->entityManager);
    }

    public function testImpliesUriTemplateParentResourceResolverInterface(): void
    {
        self::assertInstanceOf(
            UriTemplateParentResourceResolverInterface::class,
            $this->uriTemplateParentResourceResolver,
        );
    }

    public function testThrowsAnExceptionIfNoUriVariablesArePassed(): void
    {
        /** @var ResourceInterface|MockObject $itemMock */
        $itemMock = $this->createMock(ResourceInterface::class);

        $this->entityManager->expects(self::never())->method('getRepository');

        self::expectException(\RuntimeException::class);

        $this->uriTemplateParentResourceResolver->resolve($itemMock, new Post(), [], []);
    }

    public function testThrowsAnExceptionIfAnyUriVariableDoesNotMatch(): void
    {
        /** @var ResourceInterface|MockObject $itemMock */
        $itemMock = $this->createMock(ResourceInterface::class);
        /** @var ResourceInterface|MockObject $parentItemMock */
        $parentItemMock = $this->createMock(ResourceInterface::class);

        $this->entityManager->expects(self::never())->method('getRepository');

        $operation = new Post(uriVariables: [
            'variable' => new Link(fromClass: $parentItemMock::class),
        ]);

        self::expectException(\RuntimeException::class);

        $this->uriTemplateParentResourceResolver->resolve(
            $itemMock,
            $operation,
            ['uri_variables' => ['variable' => 'value']],
            [],
        );
    }

    public function testThrowsAnExceptionIfUriVariableClassIsNotDefined(): void
    {
        /** @var ResourceInterface|MockObject $itemMock */
        $itemMock = $this->createMock(ResourceInterface::class);

        $this->entityManager->expects(self::never())->method('getRepository');

        $operation = new Post(uriVariables: [
            'variable' => new Link(),
        ]);

        self::expectException(\RuntimeException::class);

        $this->uriTemplateParentResourceResolver->resolve(
            $itemMock,
            $operation,
            ['uri_variables' => ['variable' => 'value']],
            [],
        );
    }

    public function testThrowsAnExceptionIfParentResourceIsNotFound(): void
    {
        /** @var ResourceInterface|MockObject $itemMock */
        $itemMock = $this->createMock(ResourceInterface::class);
        /** @var UnitOfWork|MockObject $unitOfWorkMock */
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        /** @var EntityPersister|MockObject $entityPersisterMock */
        $entityPersisterMock = $this->createMock(EntityPersister::class);

        $parentItem = new class() implements ResourceInterface {
            public function getId(): ?int
            {
                return null;
            }
        };

        $operation = new Post(uriVariables: [
            'variable' => new Link(parameterName: 'variable', fromClass: $parentItem::class),
        ]);

        $repository = new EntityRepository($this->entityManager, new ClassMetadata($parentItem::class));

        $this->entityManager->expects(self::once())->method('getUnitOfWork')->willReturn($unitOfWorkMock);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with($parentItem::class)
            ->willReturn($repository);

        $unitOfWorkMock->expects(self::once())
            ->method('getEntityPersister')
            ->with($parentItem::class)
            ->willReturn($entityPersisterMock);

        $entityPersisterMock->expects(self::once())
            ->method('load')
            ->with(['code' => 'value'], null, null, [], null, 1, null)
            ->willReturn(null);

        self::expectException(NotFoundHttpException::class);

        $this->uriTemplateParentResourceResolver->resolve(
            $itemMock,
            $operation,
            ['uri_variables' => ['variable' => 'value']],
            [],
        );
    }

    public function testResolvesParentResource(): void
    {
        /** @var ResourceInterface|MockObject $itemMock */
        $itemMock = $this->createMock(ResourceInterface::class);
        /** @var ResourceInterface|MockObject $parentItemMock */
        $parentItemMock = $this->createMock(ResourceInterface::class);
        /** @var UnitOfWork|MockObject $unitOfWorkMock */
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        /** @var EntityPersister|MockObject $entityPersisterMock */
        $entityPersisterMock = $this->createMock(EntityPersister::class);

        $parentItemClass = new class() implements ResourceInterface {
            public function getId(): ?int
            {
                return 1;
            }
        };

        $repository = new EntityRepository($this->entityManager, new ClassMetadata($parentItemClass::class));

        $this->entityManager->expects(self::once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWorkMock);

        $this->entityManager->expects(self::once())
            ->method('getRepository')
            ->with($parentItemClass::class)
            ->willReturn($repository);

        $unitOfWorkMock->expects(self::once())
            ->method('getEntityPersister')
            ->with($parentItemClass::class)
            ->willReturn($entityPersisterMock);

        $entityPersisterMock->expects(self::once())
            ->method('load')
            ->with(['code' => 'value'], null, null, [], null, 1, null)
            ->willReturn($parentItemMock);

        $operation = new Post(uriVariables: [
            'variable' => new Link(
                parameterName: 'variable',
                fromClass: $parentItemClass::class,
            ),
        ]);

        self::assertSame(
            $parentItemMock,
            $this->uriTemplateParentResourceResolver->resolve(
                $itemMock,
                $operation,
                ['uri_variables' => ['variable' => 'value']],
            ),
        );
    }
}
