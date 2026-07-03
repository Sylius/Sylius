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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class ProductVariantWithCatalogPromotionGrid implements ProductVariantWithCatalogPromotionGridInterface
{
    public function __construct(
        private readonly string $productVariantClass,
        private readonly string $locale,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productVariantClass)
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createCatalogPromotionListQueryBuilder', [
                $this->locale,
                'expr:notFoundOnNull(service("sylius.repository.catalog_promotion").find($id))',
            ])
            ->addOrderBy('code', 'asc')

            ->withFields(
                TwigField::create('name', '@SyliusAdmin/product_variant/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.'),
                StringField::create('code')
                    ->setLabel('sylius.ui.code'),
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled'),
                TwigField::create('inventory', '@SyliusAdmin/product_variant/grid/field/inventory.html.twig')
                    ->setLabel('sylius.ui.inventory')
                    ->setPath('.'),
            )

            ->withFilters(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
                StringFilter::create('name', ['translation.name'])
                    ->setLabel('sylius.ui.name'),
            )

            ->withItemActions(
                Action::create('show_product', 'show')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_product_show',
                            'parameters' => [
                                'id' => 'resource.product.id',
                            ],
                        ],
                    ])
                    ->setLabel('sylius.ui.show_product'),
                UpdateAction::create([
                    'link' => [
                        'parameters' => [
                            'id' => 'resource.id',
                            'productId' => 'resource.product.id',
                        ],
                    ],
                ]),
            );
    }
}
