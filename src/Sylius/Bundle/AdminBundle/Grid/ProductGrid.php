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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\SubItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.product.class%', name: self::NAME)]
final class ProductGrid implements ProductGridInterface
{
    public function __construct(
        private readonly string $productClass,
        private readonly string $channelClass,
        private readonly string $taxonClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
            ])
            ->setLimits([10, 25, 50])
            ->addOrderBy('code', 'asc')
            ->withFields(
                TwigField::create('image', '@SyliusAdmin/product/grid/field/product_image.html.twig')
                    ->setLabel('sylius.ui.image')
                    ->setPath('.')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1',
                        ],
                    ]),
                TwigField::create('name', '@SyliusAdmin/product/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true, 'translation.name')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-33',
                        ],
                    ]),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
                TwigField::create('mainTaxon', '@SyliusAdmin/product/grid/field/main_taxon.html.twig')
                    ->setLabel('sylius.ui.main_taxon'),
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled'),
            )

            ->withFilters(
                StringFilter::create('search', ['code', 'translation.name'])
                    ->setLabel('sylius.ui.search')
                    ->addFormOption('type', StringFilter::TYPE_CONTAINS),
                BooleanFilter::create('enabled')
                    ->setLabel('sylius.ui.enabled'),
                EntityFilter::create('channel', $this->channelClass, fields: ['channels.id'])
                    ->setLabel('sylius.ui.channel'),
                Filter::create('taxon', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.taxon')
                    ->setFormOptions([
                        'multiple' => false,
                        'extra_options' => [
                            'class' => $this->taxonClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'fullname',
                        ],
                    ])
                    ->addOption('fields', ['productTaxons.taxon.id']),
                Filter::create('main_taxon', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.main_taxon')
                    ->setFormOptions([
                        'multiple' => false,
                        'extra_options' => [
                            'class' => $this->taxonClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'fullname',
                        ],
                    ])
                    ->addOption('fields', ['mainTaxon.id']),
            )

            ->addActionGroup(
                MainActionGroup::create(Action::create('create', 'links')
                    ->setLabel('sylius.ui.create')
                    ->setOptions([
                        'class' => 'primary',
                        'icon' => 'tabler:plus',
                        'header' => [
                            'icon' => 'tabler:cube',
                            'label' => 'sylius.ui.type',
                        ],
                        'links' => [
                            'simple' => [
                                'label' => 'sylius.ui.simple_product',
                                'route' => 'sylius_admin_product_create_simple',
                            ],
                            'configurable' => [
                                'label' => 'sylius.ui.configurable_product',
                                'route' => 'sylius_admin_product_create',
                            ],
                        ],
                    ])),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('details', 'show')
                        ->setLabel('sylius.ui.details'),
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                SubItemActionGroup::create(
                    Action::create('variants', 'list')
                        ->setLabel('sylius.ui.variants')
                        ->setOptions([
                            'links' => [
                                'index' => [
                                    'label' => 'sylius.ui.list_variants',
                                    'route' => 'sylius_admin_product_variant_index',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                                'create' => [
                                    'label' => 'sylius.ui.create',
                                    'route' => 'sylius_admin_product_variant_create',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                                'generate' => [
                                    'label' => 'sylius.ui.generate',
                                    'route' => 'sylius_admin_product_variant_generate',
                                    'visible' => 'resource.hasOptions',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                            ],
                        ]),
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
        return $this->productClass;
    }
}
