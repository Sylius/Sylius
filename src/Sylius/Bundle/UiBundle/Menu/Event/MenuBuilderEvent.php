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

namespace Sylius\Bundle\UiBundle\Menu\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class MenuBuilderEvent extends Event
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
