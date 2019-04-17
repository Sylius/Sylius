<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\PromotionInterface;

final class PromotionMenuBuilderEvent extends MenuBuilderEvent
{
    /** @var PromotionInterface */
    private $promotion;

    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        PromotionInterface $promotion
    ) {
        parent::__construct($factory, $menu);

        $this->promotion = $promotion;
    }

    public function getPromotion(): PromotionInterface
    {
        return $this->promotion;
    }
}
