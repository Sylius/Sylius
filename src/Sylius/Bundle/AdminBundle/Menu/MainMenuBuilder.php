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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class MainMenuBuilder implements MenuBuilderInterface
{
    public const EVENT_NAME = 'sylius.menu.admin.main';

    public function __construct(
        private FactoryInterface $factory,
        private ?EventDispatcherInterface $eventDispatcher = null,
        private ?RouterInterface $router = null,
    ) {
        if (null !== $this->eventDispatcher) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '2.3',
                'Passing an instance of "%s" as second argument is deprecated and will be removed in Sylius 3.0.',
                EventDispatcherInterface::class,
            );
        }

        if (null !== $this->router) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '2.3',
                'Passing an instance of "%s" as third argument is deprecated and will be removed in Sylius 3.0.',
                RouterInterface::class,
            );
        }
    }

    /** @param array<string, mixed> $options */
    public function createMenu(array $options): ItemInterface
    {
        return $this->factory->createItem('root');
    }
}
