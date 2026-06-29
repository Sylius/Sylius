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
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class InventoryGrid implements InventoryGridInterface
{
    public function __construct(
        private readonly string $productVariantClass,
        private readonly string $productClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productVariantClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('name', 'asc')
            ->setRepositoryMethod('createInventoryListQueryBuilder', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
            ])
            ->addField(
                TwigField::create('name', '@SyliusAdmin/inventory/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true, 'product.translations.name')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-75',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-25',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('inventory', '@SyliusAdmin/inventory/grid/field/inventory.html.twig')
                    ->setLabel('sylius.ui.inventory')
                    ->setPath('.'),
            )

            // -- Filters
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
            )

            ->addFilter(
                Filter::create('name', 'string')
                    ->setLabel('sylius.ui.name')
                    ->setOptions([
                        'fields' => [
                            'translation.name',
                        ],
                    ]),
            )
        ->addFilter(
            Filter::create('product', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.product')
                    ->setFormOptions([
                        'multiple' => false,
                        'extra_options' => [
                            'class' => $this->productClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'name',
                        ],
                    ])
                    ->setOptions([
                        'fields' => ['product.id'],
                    ]),
        )
            // -- Actions
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('update_product', 'update')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_product_update',
                                'parameters' => [
                                    'id' => 'resource.product.id',
                                ],
                            ],
                        ])
                        ->setLabel('sylius.ui.edit_product'),
                    UpdateAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'productId' => 'resource.product.id',
                            ],
                        ],
                    ]),
                ),
            );
    }
}
