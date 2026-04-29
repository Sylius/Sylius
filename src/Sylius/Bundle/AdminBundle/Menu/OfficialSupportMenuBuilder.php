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

final readonly class OfficialSupportMenuBuilder implements MenuBuilderInterface
{
    public function __construct(
        private MenuBuilderInterface $menuBuilder,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->menuBuilder->createMenu($options);

        $officialSupport = $menu
            ->addChild('official_support')
            ->setLabel('sylius.menu.admin.main.official_support.header')
            ->setLabelAttribute('icon', 'tabler:info-circle')
        ;

        $officialSupport
            ->addChild('sylius_plus')
            ->setUri('https://sylius.com/plus/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.sylius_plus')
            ->setLabelAttribute('icon', 'tabler:plus')
        ;

        $officialSupport
            ->addChild('browse_plugins')
            ->setUri('https://store.sylius.com/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.browse_plugins')
            ->setLabelAttribute('icon', 'tabler:plug')
        ;

        $officialSupport
            ->addChild('professional_services')
            ->setUri('https://sylius.com/services/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.professional_services')
            ->setLabelAttribute('icon', 'tabler:settings-2')
        ;

        $officialSupport
            ->addChild('find_a_partner')
            ->setUri('https://sylius.com/find-a-partner/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.find_a_partner')
            ->setLabelAttribute('icon', 'tabler:heart-handshake')
        ;

        $officialSupport
            ->addChild('sylius_certification')
            ->setUri('https://sylius.com/certification/')
            ->setLinkAttribute('target', '_blank')
            ->setLabel('sylius.menu.admin.main.official_support.sylius_certification')
            ->setLabelAttribute('icon', 'tabler:certificate')
        ;

        return $menu;
    }
}
