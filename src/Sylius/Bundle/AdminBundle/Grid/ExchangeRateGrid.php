<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Grid;

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ExchangeRateGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $currencyResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_exchange_rate';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('id', 'desc')
            ->addField(
                StringField::create('id')
                    ->setEnabled(false)
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('sourceCurrency', '@SyliusAdmin/ExchangeRate/Grid/Field/sourceCurrencyName.html.twig')
                    ->setLabel('sylius.ui.source_currency')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/ExchangeRate/Grid/Field/sourceCurrencyName.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('targetCurrency', '@SyliusAdmin/ExchangeRate/Grid/Field/targetCurrencyName.html.twig')
                    ->setLabel('sylius.ui.target_currency')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/ExchangeRate/Grid/Field/targetCurrencyName.html.twig',
                    ])
            )
            ->addField(
                StringField::create('ratio')
                    ->setLabel('sylius.ui.ratio')
                    ->setSortable(true)
            )
            ->addFilter(
                Filter::create('currency', 'entity')
                    ->setLabel('sylius.ui.currency')
                    ->setOptions([
                        'fields' => [
                            'sourceCurrency',
                            'targetCurrency',
                        ],
                    ])
                    ->setFormOptions([
                        'class' => $this->currencyResourceClass,
                        'choice_label' => 'name',
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                    DeleteAction::create(),
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                )
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
