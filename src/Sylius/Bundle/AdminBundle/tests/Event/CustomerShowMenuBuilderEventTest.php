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
use Sylius\Bundle\AdminBundle\Event\CustomerShowMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Core\Model\CustomerInterface;

final class CustomerShowMenuBuilderEventTest extends TestCase
{
    private FactoryInterface&MockObject $factory;

    private ItemInterface&MockObject $menu;

    private CustomerInterface&MockObject $customer;

    private CustomerShowMenuBuilderEvent $event;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->menu = $this->createMock(ItemInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);

        $this->event = new CustomerShowMenuBuilderEvent($this->factory, $this->menu, $this->customer);
    }

    public function testIsMenuBuilderEvent(): void
    {
        $this->assertInstanceOf(MenuBuilderEvent::class, $this->event);
    }

    public function testHasCustomer(): void
    {
        $this->assertSame($this->customer, $this->event->getCustomer());
    }
}
