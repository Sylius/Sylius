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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\ExistsFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.shipping_method.class%', name: self::NAME)]
final class ShippingMethodGrid implements ShippingMethodGridInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createListQueryBuilder', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
            ])
            ->addOrderBy('position', 'asc')

            ->withFields(
                TwigField::create('position', '@SyliusUi/grid/field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true, 'translation.name'),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
                StringField::create('zone')
                    ->setLabel('sylius.ui.zone'),
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true),
            )

            ->withFilters(
                StringFilter::create('search', ['code', 'translation.name'])
                    ->setLabel('sylius.ui.search'),
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled'),
                ExistsFilter::create('archival', 'archivedAt')
                    ->setLabel('sylius.ui.archival')
                    ->setDefaultValue(false),
            )
            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                UpdateAction::create(),
                DeleteAction::create(),
                Action::create('archive', 'archive'),
            )
            ->withBulkActions(
                DeleteAction::create(),
            )
        ;
    }
}
