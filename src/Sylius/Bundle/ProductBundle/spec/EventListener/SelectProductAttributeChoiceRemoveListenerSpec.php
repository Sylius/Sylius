<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\Mock;
use Mockery\MockInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class SelectProductAttributeChoiceRemoveListenerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductAttributeValue::class);
    }

    function it_removes_select_product_attribute_choices(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        ProductAttributeInterface $productAttribute,
        ProductAttributeValueInterface $productAttributeValue
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

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
                ]],
            ],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->willReturn($productAttributeValueRepository)
        ;
        $productAttributeValueRepository
            ->findByJsonChoiceKey('1739bc61-9e42-4c80-8b9a-f97f0579cccb')
            ->willReturn([$productAttributeValue])
        ;

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
        ProductAttributeInterface $productAttribute
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

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
        ProductAttributeInterface $productAttribute
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

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
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('wrongObject');

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->shouldNotBeCalled()
        ;
        $entityManager->flush()->shouldNotBeCalled();
    }

    function it_does_nothing_if_a_product_attribute_has_not_a_select_type(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeInterface $productAttribute
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('wrongType');

        $entityManager
            ->getRepository('Sylius\Component\Product\Model\ProductAttributeValue')
            ->shouldNotBeCalled()
        ;
        $entityManager->flush()->shouldNotBeCalled();
    }
}
