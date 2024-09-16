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
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class OrderShowMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.order.show';

    public function __construct(
        private FactoryInterface $factory,
        private EventDispatcherInterface $eventDispatcher,
        private StateMachineInterface $stateMachineFactory,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!isset($options['order'])) {
            return $menu;
        }

        $order = $options['order'];

        $menu
            ->addChild('order_history', [
                'route' => 'sylius_admin_order_history',
                'routeParameters' => ['id' => $order->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.history')
            ->setLabelAttribute('icon', 'history');

        if ($this->stateMachineFactory->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)) {
            $menu
                ->addChild('cancel', [
                    'route' => 'sylius_admin_order_cancel',
                    'routeParameters' => [
                        'id' => $order->getId(),
                        '_csrf_token' => $this->csrfTokenManager->getToken((string) $order->getId())->getValue(),
                    ],
                ])
                ->setAttribute('type', 'transition')
                ->setAttribute('confirmation', true)
                ->setLabel('sylius.ui.cancel')
                ->setLabelAttribute('icon', 'ban')
                ->setLabelAttribute('color', 'yellow');
        }

        $this->eventDispatcher->dispatch(
            new OrderShowMenuBuilderEvent($this->factory, $menu, $order, $this->stateMachineFactory),
            self::EVENT_NAME,
        );

        return $menu;
    }
}
