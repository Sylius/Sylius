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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\PromotionMenuBuilderEvent;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trigger_deprecation(
    'sylius/admin-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    PromotionUpdateMenuBuilder::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class PromotionUpdateMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.promotion.update';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
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
            new PromotionMenuBuilderEvent($this->factory, $menu, $promotion),
            self::EVENT_NAME,
        );

        return $menu;
    }

    private function addChildren(ItemInterface $menu, PromotionInterface $promotions): void
    {
        $menu
            ->addChild('manage_coupons', [
                'route' => 'sylius_admin_promotion_coupon_index',
                'routeParameters' => ['promotionId' => $promotions->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.manage_coupons')
            ->setLabelAttribute('icon', 'ticket')
            ->setLabelAttribute('color', 'gray')
        ;
    }
}
