<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Menu builder event. Used for extending the menus.
 *
 * @author Patrik Karisch <patrik.karisch@abimus.com>
 */
class MenuBuilderEvent extends Event
{
    const BACKEND_MAIN = 'sylius.menu_builder.backend.main';
    const BACKEND_SIDEBAR = 'sylius.menu_builder.backend.sidebar';
    const FRONTEND_MAIN = 'sylius.menu_builder.frontend.main';
    const FRONTEND_CURRENCY = 'sylius.menu_builder.frontend.currency';
    const FRONTEND_TAXONS = 'sylius.menu_builder.frontend.taxons';
    const FRONTEND_SOCIAL = 'sylius.menu_builder.frontend.social';
    const FRONTEND_ACCOUNT = 'sylius.menu_builder.frontend.account';

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
     * @param ItemInterface    $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
