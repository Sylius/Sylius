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
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField as SyliusTwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.product_taxon.class%', name: self::NAME)]
final class ProductTaxonGrid implements ProductTaxonGridInterface
{
    public function __construct(
        private readonly string $channelClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('position', 'asc')
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createListQueryBuilderForTaxon', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
                '$taxonId',
            ])

            ->withFields(
                SyliusTwigField::create('image', '@SyliusAdmin/product/grid/field/product_image.html.twig')
                    ->setLabel('sylius.ui.image')
                    ->setPath('product'),
                TwigField::create('name', '@SyliusAdmin/product/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('product')
                    ->setSortable(true, 'product.translations.name')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-75',
                        ],
                    ]),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setPath('product')
                    ->setSortable(true, 'product.code')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-25',
                        ],
                    ]),
                TwigField::create('mainTaxon', '@SyliusAdmin/product/grid/field/main_taxon.html.twig')
                    ->setLabel('sylius.ui.main_taxon')
                    ->setPath('product.mainTaxon'),
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setPath('product.enabled'),
                TwigField::create('position', '@SyliusAdmin/product_taxon/grid/field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setPath('.')
                    ->setSortable(true),
            )

            ->withFilters(
                StringFilter::create('search', ['product.code', 'product.translations.name'])
                    ->setLabel('sylius.ui.search')
                    ->setFormOptions([
                        'type' => StringFilter::TYPE_CONTAINS,
                    ]),
                BooleanFilter::create('enabled')
                    ->setLabel('sylius.ui.enabled')
                    ->setOptions([
                        'field' => 'product.enabled',
                    ]),
                EntityFilter::create('channel', $this->channelClass, fields: ['product.channels.id'])
                    ->setLabel('sylius.ui.channel'),
            )

            ->withMainActions(
                Action::create('update_positions', 'update_product_taxon_positions'),
            )
            ->withItemActions(
                Action::create('details', 'show')
                    ->setLabel('sylius.ui.details')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_product_show',
                            'parameters' => [
                                'id' => 'resource.product.id',
                            ],
                        ],
                    ]),
                Action::create('update', 'update')
                    ->setLabel('sylius.ui.edit')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_product_update',
                            'parameters' => [
                                'id' => 'resource.product.id',
                            ],
                        ],
                    ]),
                Action::create('delete', 'delete')
                    ->setLabel('sylius.ui.delete')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_product_delete',
                            'parameters' => [
                                'id' => 'resource.product.id',
                            ],
                        ],
                    ]),
            )
            ->withSubItemActions(
                Action::create('variants', 'list')
                    ->setLabel('sylius.ui.variants')
                    ->setOptions([
                        'links' => [
                            'index' => [
                                'label' => 'sylius.ui.list_variants',
                                'route' => 'sylius_admin_product_variant_index',
                                'parameters' => [
                                    'productId' => 'resource.product.id',
                                ],
                            ],
                            'create' => [
                                'label' => 'sylius.ui.create',
                                'route' => 'sylius_admin_product_variant_create',
                                'parameters' => [
                                    'productId' => 'resource.product.id',
                                ],
                            ],
                            'generate' => [
                                'label' => 'sylius.ui.generate',
                                'route' => 'sylius_admin_product_variant_generate',
                                'visible' => 'resource.product.hasOptions',
                                'parameters' => [
                                    'productId' => 'resource.product.id',
                                ],
                            ],
                        ],
                    ]),
            )
            ->withBulkActions(
                Action::create('delete', 'delete')
                    ->setLabel('sylius.ui.delete_products')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_product_taxon_bulk_delete_products',
                            'parameters' => [
                                'taxonId' => '$taxonId',
                            ],
                        ],
                    ]),
            )
        ;
    }
}
