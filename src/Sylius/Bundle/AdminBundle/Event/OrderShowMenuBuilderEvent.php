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
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

class OrderShowMenuBuilderEvent extends MenuBuilderEvent
{
    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        private OrderInterface $order,
        private StateMachineInterface|WinzouStateMachineInterface $stateMachine,
    ) {
        parent::__construct($factory, $menu);

        if ($this->stateMachine instanceof WinzouStateMachineInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the fourth argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    WinzouStateMachineInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function getStateMachine(): StateMachineInterface|WinzouStateMachineInterface
    {
        return $this->stateMachine;
    }
}
