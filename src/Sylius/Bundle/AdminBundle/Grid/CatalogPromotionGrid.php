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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class CatalogPromotionGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $channelResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_catalog_promotion';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('name')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('startDate')
                    ->setLabel('sylius.ui.start_date')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'Y-m-d H:i',
                    ])
            )
            ->addField(
                DateTimeField::create('endDate')
                    ->setLabel('sylius.ui.end_date')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'Y-m-d H:i',
                    ])
            )
            ->addField(
                StringField::create('priority')
                    ->setLabel('sylius.ui.priority')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('channels', '@SyliusAdmin/Grid/Field/_channels.html.twig')
                    ->setLabel('sylius.ui.channels')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Grid/Field/_channels.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/Common/Label/catalogPromotionState.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Common/Label/catalogPromotionState.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('search', 'string')
                    ->setLabel('sylius.ui.search')
                    ->setOptions([
                        'fields' => [
                            'name',
                            'code',
                        ],
                    ])
                    ->setFormOptions([
                        'type' => 'contains',
                    ])
            )
            ->addFilter(
                Filter::create('channel', 'entities')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'field' => 'channels.id',
                    ])
                    ->setFormOptions([
                        'class' => $this->channelResourceClass,
                    ])
            )
            ->addFilter(
                Filter::create('startDate', 'date')
                    ->setLabel('sylius.ui.start_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ])
            )
            ->addFilter(
                Filter::create('endDate', 'date')
                    ->setLabel('sylius.ui.end_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ])
            )
            ->addFilter(
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled')
            )
            ->addFilter(
                Filter::create('state', 'select')
                    ->setLabel('sylius.ui.state')
                    ->setFormOptions([
                        'choices' => [
                            'sylius.ui.active' => null,
                            'sylius.ui.inactive' => null,
                        ],
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                    Action::create('show_variants', 'show')
                        ->setLabel('sylius.ui.list_variants')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_catalog_promotion_product_variant_index',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                        ]),
                    UpdateAction::create(),
                    Action::create('delete', 'delete_catalog_promotion')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_catalog_promotion_delete',
                                'parameters' => [
                                    'code' => 'resource.code',
                                ],
                            ],
                            'state' => 'resource.state',
                        ]),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
