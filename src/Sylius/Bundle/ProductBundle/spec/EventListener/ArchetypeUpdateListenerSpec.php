<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeUpdateListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\EventListener\ArchetypeUpdateListener');
    }

    function let(ArchetypeBuilderInterface $builder, ObjectRepository $productRepository, ObjectManager $productManager)
    {
        $this->beConstructedWith($builder, $productRepository, $productManager);
    }

    function it_can_only_update_products_if_an_archetype_was_updated(GenericEvent $event, \stdClass $notAnArchetype, ArchetypeBuilderInterface $builder, ObjectRepository $productRepository, ObjectManager $productManager)
    {
        $event->getSubject()->willReturn($notAnArchetype);

        $productRepository->findBy(Argument::any())->shouldNotBeCalled();
        $builder->build(Argument::any())->shouldNotBeCalled();
        $productManager->persist(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(UnexpectedTypeException::class)->duringOnArchetypeUpdate($event);
    }

    function it_updates_products_with_newer_attributes_added_to_their_archetypes(
        GenericEvent $event, ArchetypeInterface $archetype, ArchetypeBuilderInterface $builder, ObjectRepository $productRepository, ObjectManager $productManager, ProductInterface $productA, ProductInterface $productB
    ) {
        $event->getSubject()->willReturn($archetype);
        $productRepository->findBy(['archetype' => $archetype])->willReturn([$productA, $productB]);

        $builder->build($productA)->shouldBeCalled();
        $builder->build($productB)->shouldBeCalled();

        $productManager->persist($productA)->shouldBeCalled();
        $productManager->persist($productB)->shouldBeCalled();

        $this->onArchetypeUpdate($event);
    }
}
