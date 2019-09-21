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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\AdministratorStatusMenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AdministratorStatusMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.administrator_status';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * AdministratorStatusMenuBuilder constructor.
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new AdministratorStatusMenuBuilderEvent($this->factory, $menu)
        );

        return $menu;
    }
}
