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

namespace Tests\Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderShowMenuBuilderEventTest extends TestCase
{
    private FactoryInterface&MockObject $factory;

    private ItemInterface&MockObject $menu;

    private MockObject&OrderInterface $order;

    private MockObject&StateMachineInterface $stateMachine;

    private OrderShowMenuBuilderEvent $event;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->menu = $this->createMock(ItemInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);

        $this->event = new OrderShowMenuBuilderEvent(
            $this->factory,
            $this->menu,
            $this->order,
            $this->stateMachine,
        );
    }

    public function testIsMenuBuilderEvent(): void
    {
        $this->assertInstanceOf(MenuBuilderEvent::class, $this->event);
    }

    public function testHasOrder(): void
    {
        $this->assertSame($this->order, $this->event->getOrder());
    }

    public function testHasStateMachine(): void
    {
        $this->assertSame($this->stateMachine, $this->event->getStateMachine());
    }
}
