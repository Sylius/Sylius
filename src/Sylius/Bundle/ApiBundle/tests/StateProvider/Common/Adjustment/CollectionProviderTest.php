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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

final class CollectionProviderTest extends TestCase
{
    private const IDENTIFIER = 'id';

    private RepositoryInterface $repository;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->repository->method('getClassName')->willReturn(OrderItem::class);
        $this->collectionProvider = new CollectionProvider($this->repository, self::IDENTIFIER);
    }

    public function testImplementsProviderInterface(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->collectionProvider);
    }

    public function testThrowsLogicExceptionWhenRepositoryIsNotForARecursiveAdjustmentsAwareResource(): void
    {
        self::expectException(\LogicException::class);

        $repositoryMock = $this->createMock(RepositoryInterface::class);

        $repositoryMock->method('getClassName')->willReturn(\stdClass::class);

        new CollectionProvider($repositoryMock, self::IDENTIFIER);
    }

    public function testThrowsExceptionWhenIdentifierIsMissingFromUriVariables(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->repository->expects($this->never())
            ->method('findOneBy');

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operation, []);
    }

    public function testThrowsExceptionWhenResourceCannotBeFound(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with([self::IDENTIFIER => 1])
            ->willReturn(null);

        self::expectException(\RuntimeException::class);

        $this->collectionProvider->provide($operation, [self::IDENTIFIER => 1]);
    }

    public function testReturnsAdjustmentsRecursively(): void
    {
        $operation = new GetCollection(class: AdjustmentInterface::class);

        $request = $this->createMock(Request::class);

        $request->query = new InputBag(['type' => 'type']);

        $orderItem = $this->createMock(OrderItem::class);

        $firstAdjustment = $this->createMock(AdjustmentInterface::class);

        $secondAdjustment = $this->createMock(AdjustmentInterface::class);

        $adjustments = new ArrayCollection([$firstAdjustment, $secondAdjustment]);

        $orderItem->expects($this->once())
            ->method('getAdjustmentsRecursively')
            ->with('type')
            ->willReturn($adjustments);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with([self::IDENTIFIER => 1])
            ->willReturn($orderItem);

        $result = $this->collectionProvider->provide(
            $operation,
            [self::IDENTIFIER => 1],
            ['request' => $request],
        );

        $this->assertSame($adjustments, $result);
    }
}
