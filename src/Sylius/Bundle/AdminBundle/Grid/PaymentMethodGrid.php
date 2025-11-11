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
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class PaymentMethodGrid extends AbstractGrid
{
    public function __construct(
        private string $paymentMethodClass,
        private string $locale,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_payment_method';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->paymentMethodClass)
            ->setRepositoryMethod('createListQueryBuilder', [$this->locale])
            ->addOrderBy('position', 'asc')
            ->setLimits([10, 25, 50])

            // -- Actions
            ->addField(
                TwigField::create('position', '@SyliusUi/grid/field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true, 'translation.name'),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true, 'code'),
            )
            ->addField(
                TwigField::create('gateway', '@SyliusUi/grid/field/humanized.html.twig')
                    ->setLabel('sylius.ui.gateway')
                    ->setPath('gatewayConfig.factoryName')
                    ->setSortable(true, 'gatewayConfig.factoryName'),
            )
            ->addField(
                TwigField::create('enabled', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            // -- Filter
            ->addFilter(
                StringFilter::create('search', ['code', 'translation.name'])
                    ->setLabel('sylius.ui.search')
                    ->setFormOptions(['type' => StringFilter::TYPE_CONTAINS]),
            )
            ->addFilter(
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled'),
            )

            // -- Actions
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('create', 'create_payment_method'),
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
