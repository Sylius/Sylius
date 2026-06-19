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
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: 'sylius_admin_exchange_rate')]
final class ExchangeRateGrid implements ExchangeRateGridInterface
{
    public function __construct(
        private readonly string $exchangeRateClass,
        private readonly string $currencyClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->exchangeRateClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('id', 'desc')

            // -- Fields
            ->addField(
                StringField::create('id')
                    ->setEnabled(false)
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('sourceCurrency')
                    ->setLabel('sylius.ui.source_currency')
                    ->setPath('sourceCurrency.name'),
            )
            ->addField(
                StringField::create('targetCurrency')
                    ->setLabel('sylius.ui.target_currency')
                    ->setPath('targetCurrency.name'),
            )
            ->addField(
                StringField::create('ratio')
                    ->setLabel('sylius.ui.ratio')
                    ->setPath('ratio')
                    ->setSortable(true),
            )

            // -- Filters
            ->addFilter(
                EntityFilter::create('currency', $this->currencyClass, fields: ['sourceCurrency', 'targetCurrency'])
                    ->setLabel('sylius.ui.currency')
                    ->addFormOption('choice_label', 'name'),
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
            )
        ;
    }
}
