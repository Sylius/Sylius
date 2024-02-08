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

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

class OrderShowMenuBuilderEvent extends MenuBuilderEvent
{
    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        private OrderInterface $order,
        private StateMachineInterface $stateMachine,
    ) {
        parent::__construct($factory, $menu);
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getStateMachine(): StateMachineInterface
    {
        return $this->stateMachine;
    }
}
