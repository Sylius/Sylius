<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\PromotionInterface;

final class PromotionMenuBuilderEventSpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, ItemInterface $menu, PromotionInterface $promotion): void
    {
        $this->beConstructedWith($factory, $menu, $promotion);
    }

    function it_is_a_manage_menu_builder_event(): void
    {
        $this->shouldHaveType(MenuBuilderEvent::class);
    }

    function it_has_a_promotion(PromotionInterface $promotion): void
    {
        $this->getPromotion()->shouldReturn($promotion);
    }
}
