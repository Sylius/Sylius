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

final readonly class DashboardMenuProvider implements MenuProviderInterface
{
    public function __invoke(ItemInterface $menu): void
    {
        $menu
            ->addChild('dashboard', ['route' => 'sylius_admin_dashboard'])
            ->setLabel('sylius.ui.dashboard')
            ->setLabelAttribute('icon', 'tabler:dashboard')
        ;
    }
}
