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
use Sylius\Bundle\AdminBundle\Menu\Provider\MenuProviderInterface;
use Webmozart\Assert\Assert;

final class CompositeMenuBuilder implements MenuBuilderInterface
{
    /**
     * @param iterable<MenuProviderInterface> $providers
     */
    public function __construct(
        private FactoryInterface $factory,
        private iterable $providers,
    ) {
        Assert::allImplementsInterface($this->providers, MenuProviderInterface::class);
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        foreach ($this->providers as $section) {
            ($section)($menu);
        }

        return $menu;
    }
}
