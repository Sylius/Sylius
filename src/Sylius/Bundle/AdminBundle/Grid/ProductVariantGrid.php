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
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class ProductVariantGrid extends AbstractGrid
{
    public function __construct(
        private readonly string $productVariantClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_variant';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productVariantClass)
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createQueryBuilderByProductId', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
                '$productId',
            ])
            ->addOrderBy('position', 'asc')
            ->addField(
                TwigField::create('name', '@SyliusAdmin/product_variant/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.'),
            )
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code'),
            )
            ->addField(
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('inventory', '@SyliusAdmin/product_variant/grid/field/inventory.html.twig')
                    ->setLabel('sylius.ui.inventory')
                    ->setPath('.')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('position', '@SyliusAdmin/product_variant/grid/field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setPath('.')
                    ->setSortable(true, 'position')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
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
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('generate', 'generate_variants')
                        ->setOptions([
                            'product' => 'expr:service(\'sylius.repository.product\').find($productId)',
                        ]),
                    Action::create('update_positions', 'update_product_variant_positions'),
                    CreateAction::create()
                        ->setOptions([
                            'link' => [
                                'parameters' => [
                                    'productId' => '$productId',
                                ],
                            ],
                        ]),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'productId' => '$productId',
                            ],
                        ],
                    ]),
                    DeleteAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'productId' => '$productId',
                            ],
                        ],
                    ]),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create([
                        'link' => [
                            'parameters' => [
                                'productId' => '$productId',
                            ],
                        ],
                    ]),
                ),
            )
            ->addActionGroup(
                ActionGroup::create(
                    'subitem',
                    Action::create('price_history', 'price_history')
                        ->setLabel('sylius.ui.price_history'),
                ),
            )
        ;
    }
}
