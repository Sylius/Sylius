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

namespace Tests\Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeValueRepository;
use Sylius\Bundle\ProductBundle\EventListener\SelectProductAttributeChoiceRemoveListener;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class SelectProductAttributeChoiceRemoveListenerTest extends TestCase
{
    private LifecycleEventArgs&MockObject $event;

    private EntityManagerInterface&MockObject $entityManager;

    private SelectProductAttributeChoiceRemoveListener $selectProductAttributeChoiceRemoveListener;

    protected function setUp(): void
    {
        $this->event = $this->createMock(LifecycleEventArgs::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->selectProductAttributeChoiceRemoveListener = new SelectProductAttributeChoiceRemoveListener(ProductAttributeValue::class);
    }

    public function testRemovesSelectProductAttributeChoices(): void
    {
        /** @var ProductAttributeInterface&MockObject $productAttribute */
        $productAttribute = $this->createMock(ProductAttributeInterface::class);
        /** @var ProductAttributeValueInterface&MockObject $productAttributeValue */
        $productAttributeValue = $this->createMock(ProductAttributeValueInterface::class);
        /** @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);
        /** @var Query&MockObject $query */
        $query = $this->createMock(Query::class);
        /** @var UnitOfWork&MockObject $unitOfWork */
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $productAttributeValueRepository = new ProductAttributeValueRepository($this->entityManager, new ClassMetadata(ProductAttributeValue::class));

        $this->event->expects($this->once())->method('getObject')->willReturn($productAttribute);
        $this->event->expects($this->once())->method('getObjectManager')->willReturn($this->entityManager);
        $productAttribute->expects($this->once())->method('getType')->willReturn(SelectAttributeType::TYPE);

        $unitOfWork->expects($this->once())
            ->method('getEntityChangeSet')
            ->with($productAttribute)
            ->willReturn([
                'configuration' => [
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                        '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                    ]],
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    ]],
                ],
            ])
        ;

        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWork);
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(ProductAttributeValue::class)
            ->willReturn($productAttributeValueRepository)
        ;
        $this->entityManager->expects($this->once())->method('createQueryBuilder')->willReturn($queryBuilder);
        $this->entityManager->expects($this->once())->method('flush');

        $queryBuilder->expects($this->once())->method('select')->with('o')->willReturn($queryBuilder);
        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(ProductAttributeValue::class, 'o', null)
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->expects($this->once())->method('andWhere')->with($this->isType('string'))->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('setParameter')->with('key', '%"1739bc61-9e42-4c80-8b9a-f97f0579cccb"%')->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('getQuery')->willReturn($query);

        $query->expects($this->once())->method('getResult')->willReturn([$productAttributeValue]);

        $productAttributeValue->expects($this->once())->method('getValue')->willReturn([
            '8ec40814-adef-4194-af91-5559b5f19236',
            '1739bc61-9e42-4c80-8b9a-f97f0579cccb',
        ]);
        $productAttributeValue->expects($this->once())->method('setValue')->with(['8ec40814-adef-4194-af91-5559b5f19236']);

        $this->selectProductAttributeChoiceRemoveListener->postUpdate($this->event);
    }

    public function testDoesNotRemoveSelectProductAttributeChoicesIfThereIsOnlyAddedNewChoice(): void
    {
        /** @var ProductAttributeInterface&MockObject $productAttribute */
        $productAttribute = $this->createMock(ProductAttributeInterface::class);
        /** @var UnitOfWork&MockObject $unitOfWork */
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $this->event->expects($this->once())->method('getObject')->willReturn($productAttribute);
        $this->event->expects($this->once())->method('getObjectManager')->willReturn($this->entityManager);

        $productAttribute->expects($this->once())->method('getType')->willReturn(SelectAttributeType::TYPE);

        $unitOfWork->expects($this->once())
            ->method('getEntityChangeSet')
            ->with($productAttribute)
            ->willReturn([
                'configuration' => [
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    ]],
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                        '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                    ]],
                ],
            ])
        ;

        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWork);
        $this->entityManager->expects($this->never())->method('getRepository')->with(ProductAttributeValue::class);
        $this->entityManager->expects($this->never())->method('flush');

        $this->selectProductAttributeChoiceRemoveListener->postUpdate($this->event);
    }

    public function testDoesNotRemoveSelectProductAttributeChoicesIfThereIsOnlyChangedValue(): void
    {
        /** @var ProductAttributeInterface&MockObject $productAttribute */
        $productAttribute = $this->createMock(ProductAttributeInterface::class);
        /** @var UnitOfWork&MockObject $unitOfWork */
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $this->event->expects($this->once())->method('getObject')->willReturn($productAttribute);
        $this->event->expects($this->once())->method('getObjectManager')->willReturn($this->entityManager);

        $productAttribute->expects($this->once())->method('getType')->willReturn(SelectAttributeType::TYPE);

        $unitOfWork->expects($this->once())
            ->method('getEntityChangeSet')
            ->with($productAttribute)
            ->willReturn([
                'configuration' => [
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                        '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                    ]],
                    ['choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                        '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Watermelon',
                    ]],
                ],
            ])
        ;

        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWork);
        $this->entityManager->expects($this->never())->method('getRepository')->with(ProductAttributeValue::class);
        $this->entityManager->expects($this->never())->method('flush');

        $this->selectProductAttributeChoiceRemoveListener->postUpdate($this->event);
    }

    public function testDoesNothingIfAnEntityIsNotAProductAttribute(): void
    {
        $this->event->expects($this->once())->method('getObject')->willReturn('wrongObject');

        $this->entityManager->expects($this->never())->method('getRepository')->with(ProductAttributeValue::class);
        $this->entityManager->expects($this->never())->method('flush');

        $this->selectProductAttributeChoiceRemoveListener->postUpdate($this->event);
    }

    public function testDoesNothingIfAProductAttributeHasNotASelectType(): void
    {
        /** @var ProductAttributeInterface&MockObject $productAttribute */
        $productAttribute = $this->createMock(ProductAttributeInterface::class);

        $this->event->expects($this->once())->method('getObject')->willReturn($productAttribute);
        $productAttribute->expects($this->once())->method('getType')->willReturn('wrongType');

        $this->entityManager->expects($this->never())->method('getRepository')->with(ProductAttributeValue::class);
        $this->entityManager->expects($this->never())->method('flush');

        $this->selectProductAttributeChoiceRemoveListener->postUpdate($this->event);
    }
}
