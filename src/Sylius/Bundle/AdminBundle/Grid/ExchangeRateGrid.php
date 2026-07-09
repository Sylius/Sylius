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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\EntityFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.exchange_rate.class%', name: self::NAME)]
final class ExchangeRateGrid implements ExchangeRateGridInterface
{
    public function __construct(
        private readonly string $currencyClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 25, 50])
            ->addOrderBy('id', 'desc')

            ->withFields(
                StringField::create('id')
                    ->setEnabled(false)
                    ->setSortable(true),
                StringField::create('sourceCurrency')
                    ->setLabel('sylius.ui.source_currency')
                    ->setPath('sourceCurrency.name'),
                StringField::create('targetCurrency')
                    ->setLabel('sylius.ui.target_currency')
                    ->setPath('targetCurrency.name'),
                StringField::create('ratio')
                    ->setLabel('sylius.ui.ratio')
                    ->setPath('ratio')
                    ->setSortable(true),
            )

            ->withFilters(
                EntityFilter::create('currency', $this->currencyClass, fields: ['sourceCurrency', 'targetCurrency'])
                    ->setLabel('sylius.ui.currency')
                    ->addFormOption('choice_label', 'name'),
            )

            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                UpdateAction::create(),
                DeleteAction::create(),
            )
            ->withBulkActions(
                DeleteAction::create(),
            )
        ;
    }
}
