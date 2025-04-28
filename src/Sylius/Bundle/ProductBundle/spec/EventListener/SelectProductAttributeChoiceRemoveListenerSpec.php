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

namespace spec\Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Mockery;
use Mockery\MockInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeValueRepository;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class SelectProductAttributeChoiceRemoveListenerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductAttributeValue::class);
    }

    function it_removes_select_product_attribute_choices(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeInterface $productAttribute,
        ProductAttributeValueInterface $productAttributeValue,
        QueryBuilder $queryBuilder,
        Connection $connection,
        Query $query,
    ): void {
        $event->getObject()->willReturn($productAttribute);
        $event->getObjectManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        $productAttributeValueRepository = new ProductAttributeValueRepository($entityManager->getWrappedObject(), new ClassMetadata('Sylius\Component\Product\Model\ProductAttributeValue'));

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$productAttribute->getWrappedObject()])->andReturn([
            'configuration' => [
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                ]],
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                ]],
            ],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $entityManager->getConnection()->willReturn($connection);

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->willReturn($productAttributeValueRepository)
        ;

        $entityManager->createQueryBuilder()->willReturn($queryBuilder);
        $queryBuilder->select(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->from(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->andWhere(Argument::type('string'))->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('key', '%"1739bc61-9e42-4c80-8b9a-f97f0579cccb"%')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);

        $query->getResult()->willReturn([$productAttributeValue]);

        $productAttributeValue->getValue()->willReturn([
            '8ec40814-adef-4194-af91-5559b5f19236',
            '1739bc61-9e42-4c80-8b9a-f97f0579cccb',
        ]);

        $productAttributeValue->setValue(['8ec40814-adef-4194-af91-5559b5f19236'])->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->postUpdate($event);
    }

    function it_does_not_remove_select_product_attribute_choices_if_there_is_only_added_new_choice(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeInterface $productAttribute,
    ): void {
        $event->getObject()->willReturn($productAttribute);
        $event->getObjectManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$productAttribute->getWrappedObject()])->andReturn([
            'configuration' => [
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                ]],
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                ]],
            ],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $entityManager->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_not_remove_select_product_attribute_choices_if_there_is_only_changed_value(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeInterface $productAttribute,
    ): void {
        $event->getObject()->willReturn($productAttribute);
        $event->getObjectManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$productAttribute->getWrappedObject()])->andReturn([
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
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $entityManager->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')->shouldNotBeCalled();
        $entityManager->flush()->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_if_an_entity_is_not_a_product_attribute(
        EntityManagerInterface $entityManager,
        LifecycleEventArgs $event,
    ): void {
        $event->getObject()->willReturn('wrongObject');

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->shouldNotBeCalled()
        ;
        $entityManager->flush()->shouldNotBeCalled();
    }

    function it_does_nothing_if_a_product_attribute_has_not_a_select_type(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeInterface $productAttribute,
    ): void {
        $event->getObject()->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('wrongType');

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->shouldNotBeCalled()
        ;
        $entityManager->flush()->shouldNotBeCalled();
    }
}
