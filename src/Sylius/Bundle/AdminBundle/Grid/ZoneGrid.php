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

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ZoneGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_zone';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
            )
            ->addField(
                StringField::create('name')
                    ->setLabel('sylius.ui.name')
            )
            ->addField(
                TwigField::create('type', '@SyliusUi/Grid/Field/label.html.twig')
                    ->setLabel('sylius.ui.type')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/label.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('name', 'string')
                    ->setLabel('sylius.ui.name')
            )
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('create', 'links')
                        ->setLabel('sylius.ui.create')
                        ->setOptions([
                            'class' => 'primary',
                            'icon' => 'plus',
                            'header' => [
                                'icon' => 'cube',
                                'label' => 'sylius.ui.type',
                            ],
                            'links' => [
                                'country' => [
                                    'label' => 'sylius.ui.zone_consisting_of_countries',
                                    'icon' => 'plus',
                                    'route' => 'sylius_admin_zone_create',
                                    'parameters' => [
                                        'type' => 'country',
                                    ],
                                ],
                                'province' => [
                                    'label' => 'sylius.ui.zone_consisting_of_provinces',
                                    'icon' => 'plus',
                                    'route' => 'sylius_admin_zone_create',
                                    'parameters' => [
                                        'type' => 'province',
                                    ],
                                ],
                                'zone' => [
                                    'label' => 'sylius.ui.zone_consisting_of_other_zones',
                                    'icon' => 'plus',
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

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
