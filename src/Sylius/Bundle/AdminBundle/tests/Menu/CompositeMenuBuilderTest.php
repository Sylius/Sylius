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
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Menu\CompositeMenuBuilder;
use Sylius\Bundle\AdminBundle\Menu\Provider\MenuProviderInterface;

final class CompositeMenuBuilderTest extends TestCase
{
    public function testCallsProvidersInOrderOnTheSameRootItem(): void
    {
        $builder = new CompositeMenuBuilder(new MenuFactory(), [
            $this->createProvider('first'),
            $this->createProvider('second'),
        ]);

        $menu = $builder->createMenu([]);

        $this->assertSame('root', $menu->getName());
        $this->assertSame(['first', 'second'], array_keys($menu->getChildren()));
    }

    public function testThrowsExceptionWhenProviderDoesNotImplementInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CompositeMenuBuilder(new MenuFactory(), [new \stdClass()]);
    }

    private function createProvider(string $childName): MenuProviderInterface
    {
        return new class($childName) implements MenuProviderInterface {
            public function __construct(private readonly string $childName)
            {
            }

            public function __invoke(ItemInterface $menu): void
            {
                $menu->addChild($this->childName);
            }
        };
    }
}
