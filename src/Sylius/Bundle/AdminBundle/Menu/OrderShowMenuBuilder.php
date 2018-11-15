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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class OrderShowMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.order.show';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        StateMachineFactoryInterface $stateMachineFactory,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->csrfTokenManager = $csrfTokenManager;
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
            ->setLabelAttribute('icon', 'history')
        ;

        $stateMachine = $this->stateMachineFactory->get($order, OrderTransitions::GRAPH);
        if ($stateMachine->can(OrderTransitions::TRANSITION_CANCEL)) {
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
                ->setLabelAttribute('color', 'yellow')
            ;
        }

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new OrderShowMenuBuilderEvent($this->factory, $menu, $order, $stateMachine)
        );

        return $menu;
    }
}
