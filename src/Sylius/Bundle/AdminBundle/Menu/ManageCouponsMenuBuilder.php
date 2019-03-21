<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\ManageCouponsMenuBuilderEvent;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ManageCouponsMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.promotion.show';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        if (!isset($options['promotion'])) {
            return $menu;
        }
        $promotion = $options['promotion'];
        $this->addChildren($menu, $promotion);
        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ManageCouponsMenuBuilderEvent($this->factory, $menu, $promotion)
        );
        return $menu;
    }

    private function addChildren(ItemInterface $menu, PromotionInterface $promotions): void
    {
        $menu
            ->addChild('promotion_validate', [
                'route' => 'sylius_admin_promotion_coupon_index',
                'routeParameters' => ['promotionId' => $promotions->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.manage_coupons')
            ->setLabelAttribute('icon', 'check')
            ->setLabelAttribute('color', 'gray')
        ;
    }
}
