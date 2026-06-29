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

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class ZoneGrid implements ZoneGridInterface
{
    public function __construct(
        private readonly string $resourceClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->resourceClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('priority', 'desc')

            // -- Fields
            ->addField(
                TwigField::create('priority', '@SyliusAdmin/zone/grid/field/priority.html.twig')
                    ->setLabel('sylius.ui.priority')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name'),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code'),
            )
            ->addField(
                TwigField::create('type', '@SyliusAdmin/zone/grid/field/type.html.twig')
                    ->setLabel('sylius.ui.type'),
            )

            // --- Filters
            ->addFilter(
                StringFilter::create('name')
                    ->setLabel('sylius.ui.name'),
            )
            ->addFilter(
                StringFilter::create('code')
                ->setLabel('sylius.ui.code'),
            )

            // -- Actions
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('create', 'links')
                        ->setLabel('sylius.ui.create')
                        ->setOptions([
                            'class' => 'primary',
                            'icon' => 'tabler:plus',
                            'header' => [
                                'icon' => 'tabler:cube',
                                'label' => 'sylius.ui.type',
                            ],
                            'links' => [
                                'country' => [
                                    'label' => 'sylius.ui.zone_consisting_of_countries',
                                    'icon' => 'tabler:plus',
                                    'route' => 'sylius_admin_zone_create',
                                    'parameters' => [
                                        'type' => 'country',
                                    ],
                                ],
                                'province' => [
                                    'label' => 'sylius.ui.zone_consisting_of_provinces',
                                    'icon' => 'tabler:plus',
                                    'route' => 'sylius_admin_zone_create',
                                    'parameters' => [
                                        'type' => 'province',
                                    ],
                                ],
                                'zone' => [
                                    'label' => 'sylius.ui.zone_consisting_of_other_zones',
                                    'icon' => 'tabler:plus',
                                    'route' => 'sylius_admin_zone_create',
                                    'parameters' => [
                                        'type' => 'zone',
                                    ],
                                ],
                            ],
                        ]),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            );
    }
}
