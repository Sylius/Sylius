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
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ProductBundle\Remover\SelectProductAttributeValuesRemoverInterface;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class SelectProductAttributeChoiceRemoveListenerSpec extends ObjectBehavior
{
    function let(SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover): void
    {
        $this->beConstructedWith($selectProductAttributeValuesRemover);
    }

    function it_removes_select_product_attribute_choices(
        SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover,
        LifecycleEventArgs $event,
        ProductAttributeInterface $productAttribute,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getEntityChangeSet($productAttribute)->willReturn([
            'configuration' => [
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                ]],
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                ]],
            ]
        ]);

        $selectProductAttributeValuesRemover->removeValues(['1739bc61-9e42-4c80-8b9a-f97f0579cccb'])->shouldBeCalled();

        $this->postUpdate($event);
    }

    function it_does_not_remove_select_product_attribute_choices_if_there_is_only_added_new_choice(
        SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover,
        LifecycleEventArgs $event,
        ProductAttributeInterface $productAttribute,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getEntityChangeSet($productAttribute)->willReturn([
            'configuration' => [
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                ]],
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                ]],
            ]
        ]);

        $selectProductAttributeValuesRemover->removeValues(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_not_remove_select_product_attribute_choices_if_there_is_only_changed_value(
        SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover,
        LifecycleEventArgs $event,
        ProductAttributeInterface $productAttribute,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getEntityChangeSet($productAttribute)->willReturn([
            'configuration' => [
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                ]],
                ['choices' => [
                    '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Watermelon',
                ]],
            ]
        ]);

        $selectProductAttributeValuesRemover->removeValues(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_if_an_entity_is_not_a_product_attribute(
        SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('wrongObject');

        $selectProductAttributeValuesRemover->removeValues(Argument::any())->shouldNotBeCalled();
    }

    function it_does_nothing_if_a_product_attribute_has_not_a_select_type(
        SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover,
        LifecycleEventArgs $event,
        ProductAttributeInterface $productAttribute
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $productAttribute->getType()->willReturn('wrongType');

        $selectProductAttributeValuesRemover->removeValues(Argument::any())->shouldNotBeCalled();
    }
}
