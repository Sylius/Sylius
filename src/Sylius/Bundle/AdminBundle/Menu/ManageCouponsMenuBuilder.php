<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\ManageCouponsMenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ManageCouponsMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.coupon.show';

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
        if (!isset($options['coupon'])) {
            return $menu;
        }
        $coupon = $options['coupon'];
        $this->addChildren($menu, $coupon);
        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ManageCouponsMenuBuilderEvent($this->factory, $menu, $coupon)
        );
        return $menu;
    }

    private function addChildren(ItemInterface $menu, Coupon $coupon): void
    {
        $menu
            ->addChild('coupon_validate', [
                'route' => 'app_coupon_validate',
                'routeParameters' => ['id' => $coupon->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.coupon.enabled')
            ->setLabelAttribute('icon', 'check')
            ->setLabelAttribute('color', 'green')
        ;
        $menu->addChild('coupon_cancel', [ /* ... */ ]);
    }
}
