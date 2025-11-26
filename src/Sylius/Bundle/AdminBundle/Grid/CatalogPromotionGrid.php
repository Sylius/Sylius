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
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;

#[AsGrid(name: 'sylius_admin_catalog_promotion')]
final class CatalogPromotionGrid extends AbstractGrid implements CatalogPromotionGridInterface
{
    public function __construct(
        private readonly string $catalogPromotionClass,
        private readonly string $channelClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->catalogPromotionClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('code', 'asc')

            //-- Fields
            ->addField(
                TwigField::create('priority', '@SyliusAdmin/catalog_promotion/grid/field/priority.html.twig')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ])
                    ->setLabel('sylius.ui.priority')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('startDate', '@SyliusAdmin/catalog_promotion/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.start_date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('endDate', '@SyliusAdmin/catalog_promotion/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.end_date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('channels', '@SyliusAdmin/shared/grid/field/channels.html.twig')
                    ->setLabel('sylius.ui.channels')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/catalog_promotion/grid/field/state.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )

            // --- Filters
            ->addFilter(
                StringFilter::create(
                    'search',
                    fields: ['name', 'code'],
                    type: 'contains',
                )
                    ->setLabel('sylius.ui.search'),
            )
            ->addFilter(
                EntityFilter::create(
                    'channel',
                    resourceClass: $this->channelClass,
                    fields: ['channels.id'],
                )
                    ->setLabel('sylius.ui.channel'),
            )
            ->addFilter(
                DateFilter::create('startDate')
                    ->setLabel('sylius.ui.start_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ]),
            )
            ->addFilter(
                DateFilter::create('endDate')
                    ->setLabel('sylius.ui.end_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ]),
            )
            ->addFilter(
                BooleanFilter::create('enabled')
                    ->setLabel('sylius.ui.enabled'),
            )
            ->addFilter(
                Filter::create('state', 'select')
                    ->setLabel('sylius.ui.state')
                    ->addFormOption('choices', [
                        'sylius.ui.active' => CatalogPromotionStates::STATE_ACTIVE,
                        'sylius.ui.inactive' => CatalogPromotionStates::STATE_INACTIVE,
                    ]),
            )

            // -- Actions
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
                        ->setIcon('tabler:list-letters')
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
}
