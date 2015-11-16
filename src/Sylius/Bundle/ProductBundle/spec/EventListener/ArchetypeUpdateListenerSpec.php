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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeUpdateListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\EventListener\ArchetypeUpdateListener');
    }

    function let(ArchetypeBuilderInterface $builder, ResourceRepositoryInterface $productRepository, ResourceManagerInterface $productManager)
    {
        $this->beConstructedWith($builder, $productRepository, $productManager);
    }

    function it_can_only_update_products_if_an_archetype_was_updated(ResourceEvent $event, \stdClass $notAnArchetype, ArchetypeBuilderInterface $builder, ResourceRepositoryInterface $productRepository, ResourceManagerInterface $productManager)
    {
        $event->getResource()->willReturn($notAnArchetype);

        $productRepository->findBy(Argument::any())->shouldNotBeCalled();
        $builder->build(Argument::any())->shouldNotBeCalled();
        $productManager->persist(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow('Sylius\Component\Resource\Exception\UnexpectedTypeException')->duringOnArchetypeUpdate($event);
    }

    function it_updates_products_with_newer_attributes_added_to_their_archetypes(
        ResourceEvent $event, ArchetypeInterface $archetype, ArchetypeBuilderInterface $builder, ResourceRepositoryInterface $productRepository, ResourceManagerInterface $productManager, ProductInterface $productA, ProductInterface $productB
    ) {
        $event->getResource()->willReturn($archetype);
        $productRepository->findBy(array('archetype' => $archetype))->willReturn(array($productA, $productB));

        $builder->build($productA)->shouldBeCalled();
        $builder->build($productB)->shouldBeCalled();

        $productManager->persist($productA)->shouldBeCalled();
        $productManager->persist($productB)->shouldBeCalled();

        $productManager->flush()->shouldBeCalled();

        $this->onArchetypeUpdate($event);
    }
}
