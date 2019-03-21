<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\spec\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\Event\ManageCouponsMenuBuilderEvent;
use Sylius\Component\Core\Model\PromotionInterface;

final class ManageCouponsMenuBuilderEventSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, ItemInterface $menu, PromotionInterface $promotion): void
    {
        $this->beConstructedWith($factory, $menu, $promotion);
    }

    function it_is_a_menu_builder_event(): void
    {
        $this->shouldHaveType(ManageCouponsMenuBuilderEvent::class);
    }
}
