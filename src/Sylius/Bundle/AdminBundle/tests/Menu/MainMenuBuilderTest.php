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

namespace Tests\Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Menu\MainMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\MenuBuilderInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

final class MainMenuBuilderTest extends TestCase
{
    public function testDelegatesToMenuBuilder(): void
    {
        $menu = $this->createStub(ItemInterface::class);

        $menuBuilder = $this->createMock(MenuBuilderInterface::class);
        $menuBuilder->expects($this->once())->method('createMenu')->with(['foo' => 'bar'])->willReturn($menu);

        $mainMenuBuilder = new MainMenuBuilder($menuBuilder);

        $this->assertSame($menu, $mainMenuBuilder->createMenu(['foo' => 'bar']));
    }

    #[IgnoreDeprecations]
    public function testBuildsMenuAndDispatchesEventWhenFactoryIsPassed(): void
    {
        $this->expectUserDeprecationMessage(sprintf(
            'Since sylius/admin-bundle 2.3: Passing an instance of "Knp\Menu\FactoryInterface" as the first argument of "%s" is deprecated and will be removed in Sylius 3.0. Pass a "%s" instance instead.',
            MainMenuBuilder::class,
            MenuBuilderInterface::class,
        ));

        $factory = new MenuFactory();

        $router = $this->createStub(RouterInterface::class);
        $router->method('generate')->willReturn('/admin/');

        $dispatchedMenu = null;
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(MenuBuilderEvent::class), MainMenuBuilder::EVENT_NAME)
            ->willReturnCallback(function (MenuBuilderEvent $event) use (&$dispatchedMenu): MenuBuilderEvent {
                $dispatchedMenu = $event->getMenu();

                return $event;
            })
        ;

        $menu = (new MainMenuBuilder($factory, $eventDispatcher, $router))->createMenu([]);

        $this->assertSame($menu, $dispatchedMenu);
        $this->assertSame(
            ['dashboard', 'catalog', 'sales', 'customers', 'marketing', 'configuration', 'official_support', 'sylius.ui.administration'],
            array_keys($menu->getChildren()),
        );
    }
}
