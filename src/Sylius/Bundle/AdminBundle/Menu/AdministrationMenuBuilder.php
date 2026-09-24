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

use Knp\Menu\ItemInterface;

final readonly class AdministrationMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private MenuBuilderInterface $menuBuilder,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $administration = $menu
            ->addChild('sylius.ui.administration')
            ->setLabel('sylius.ui.administration')
            ->setLabelAttribute('icon', 'tabler:lock')
        ;

        $administration
            ->addChild('roles')
            ->setUri('https://sylius.com/plus/?utm_source=product&utm_medium=placeholder&utm_campaign=rbac-placeholder')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.ui.roles')
            ->setExtra('plus_logo', true)
        ;

        return $menu;
    }
}
