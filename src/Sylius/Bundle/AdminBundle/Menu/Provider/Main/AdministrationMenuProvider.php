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

namespace Sylius\Bundle\AdminBundle\Menu\Provider\Main;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Menu\Provider\MenuProviderInterface;

final readonly class AdministrationMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
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
    }
}
