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

namespace Sylius\Bundle\ShopBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class ProductGrid implements ProductGridInterface
{
    public function __construct(
        private readonly string $productClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createShopListQueryBuilder', [
               'channel' => "expr:service('sylius.context.channel').getChannel()",
               'taxon' => "expr:notFoundOnNull(service('sylius.repository.taxon').findOneBySlug(\$slug, service('sylius.context.locale').getLocaleCode()))",
               'locale' => "expr:service('sylius.context.locale').getLocaleCode()",
               'sorting' => "expr:service('request_stack').getCurrentRequest().query.all('sorting')",
               'includeAllDescendants' => "expr:parameter('sylius_shop.product_grid.include_all_descendants')",
            ])
            ->setDriverOption('class', $this->productClass)
            ->orderBy('position', 'asc')
            ->setLimits([
                9,
                18,
                27,
            ])
            ->withFields(
                DateTimeField::create('createdAt')
                    ->setSortable(true),
                StringField::create('position')
                    ->setSortable(true, 'productTaxon.position'),
                StringField::create('name')
                    ->setSortable(true, 'translation.name'),
                Field::create('price', 'int')
                    ->setSortable(true, 'channelPricing.price'),
            )
            ->withFilters(
                Filter::create('search', 'shop_string')
                    ->setLabel(false)
                    ->addOption('fields', ['translation.name'])
                    ->addFormOption('type', 'contains'),
            );
    }
}
