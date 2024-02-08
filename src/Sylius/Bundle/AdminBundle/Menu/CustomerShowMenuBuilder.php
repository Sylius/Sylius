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
use Sylius\Bundle\AdminBundle\Event\CustomerShowMenuBuilderEvent;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CustomerShowMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.customer.show';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!isset($options['customer'])) {
            return $menu;
        }

        $customer = $options['customer'];
        $this->addChildren($menu, $customer);

        $this->eventDispatcher->dispatch(
            new CustomerShowMenuBuilderEvent($this->factory, $menu, $customer),
            self::EVENT_NAME,
        );

        return $menu;
    }

    private function addChildren(ItemInterface $menu, CustomerInterface $customer): void
    {
        if (null !== $customer->getUser()) {
            $menu->setExtra('column_id', 'actions');

            $menu
                ->addChild('update', [
                    'route' => 'sylius_admin_customer_update',
                    'routeParameters' => ['id' => $customer->getId()],
                ])
                ->setAttribute('type', 'edit')
                ->setLabel('sylius.ui.edit')
            ;

            $menu
                ->addChild('order_index', [
                    'route' => 'sylius_admin_customer_order_index',
                    'routeParameters' => ['id' => $customer->getId()],
                ])
                ->setAttribute('type', 'show')
                ->setLabel('sylius.ui.show_orders')
            ;

            $menu
                ->addChild('user_delete', [
                    'route' => 'sylius_admin_shop_user_delete',
                    'routeParameters' => ['id' => $customer->getUser()->getId()],
                ])
                ->setAttribute('type', 'delete')
                ->setAttribute('resource_id', $customer->getUser()->getId())
                ->setLabel('sylius.ui.delete')
            ;

            return;
        }

        $menu->setExtra('column_id', 'no-account');

        $menu
            ->addChild('order_index', [
                'route' => 'sylius_admin_customer_order_index',
                'routeParameters' => ['id' => $customer->getId()],
            ])
            ->setAttribute('type', 'show')
            ->setLabel('sylius.ui.show_orders')
        ;
    }
}
