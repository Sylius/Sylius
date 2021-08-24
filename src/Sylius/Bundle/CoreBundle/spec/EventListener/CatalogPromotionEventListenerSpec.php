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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\CatalogPromotionEventListener;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionEventListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $bus): void
    {
        $this->beConstructedWith($bus);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CatalogPromotionEventListener::class);
    }

    function it_sends_catalog_promotion_updated_after_persist_catalog_promotion(
        MessageBusInterface $bus,
        LifecycleEventArgs $args,
        CatalogPromotionInterface $entity
    ): void {
        $args->getObject()->willReturn($entity);
        $entity->getCode()->willReturn('winter_sale');
        $message = new CatalogPromotionUpdated('winter_sale');
        $bus->dispatch($message)->willReturn(new Envelope($message));

        $this->postPersist($args);
    }

    function it_does_not_send_catalog_promotion_updated_after_persist_other_entity(
        MessageBusInterface $bus,
        LifecycleEventArgs $args
    ): void {
        $args->getObject()->willReturn(new \stdClass());
        $bus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($args);
    }
}
