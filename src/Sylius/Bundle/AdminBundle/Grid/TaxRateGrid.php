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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class TaxRateGrid extends AbstractGrid
{
    public function __construct(
        private string $taxRateClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_tax_rate';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->taxRateClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('name', 'asc')

            // -- Fields
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
                StringField::create('zone')
                    ->setLabel('sylius.ui.zone'),
            )
            ->addField(
                StringField::create('category')
                    ->setLabel('sylius.ui.category'),
            )
            ->addField(
                TwigField::create('amount', '@SyliusUi/grid/field/percent.html.twig')
                    ->setLabel('sylius.ui.amount')
                    ->setSortable(true),
            )
            ->addFilter(
                Filter::create('startDate', 'date')
                    ->setLabel('sylius.ui.start_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ]),
            )

            // -- Filters
            ->addFilter(
                DateFilter::create('endDate')
                    ->setLabel('sylius.ui.end_date')
                    ->setOptions([
                        'inclusive_to' => true,
                    ]),
            )
            ->addFilter(
                StringFilter::create('search', ['code', 'name'])
                    ->setLabel('sylius.ui.search'),
            )

            // -- Actions
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
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
}
