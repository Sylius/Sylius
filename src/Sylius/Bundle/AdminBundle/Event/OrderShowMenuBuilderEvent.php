<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class OrderShowMenuBuilderEvent extends MenuBuilderEvent
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var StateMachineInterface
     */
    private $stateMachine;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param OrderInterface $order
     * @param StateMachineInterface $stateMachine
     */
    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ) {
        parent::__construct($factory, $menu);

        $this->order = $order;
        $this->stateMachine = $stateMachine;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return StateMachineInterface
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }
}
