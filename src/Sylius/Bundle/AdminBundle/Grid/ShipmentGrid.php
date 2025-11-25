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
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: 'sylius_admin_shipment')]
final class ShipmentGrid extends AbstractGrid implements ShipmentGridInterface
{
    public function __construct(
        private readonly string $shipmentClass,
        private readonly string $channelClass,
        private readonly string $shippingMethodClass,
    ) {
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->shipmentClass)
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createListQueryBuilder')
            ->addOrderBy('createdAt', 'desc')

            // -- Fields
            ->addField(
                TwigField::create('createdAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.created_at')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('shippedAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.shipped_at')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('order', '@SyliusAdmin/shared/grid/field/order_number.html.twig')
                    ->setLabel('sylius.ui.order')
                    ->setPath('order')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/shared/grid/field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setPath('order.channel'),
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/shared/grid/field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setPath('order.channel'),
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/shared/grid/field/shipment_state.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addFilter(
                SelectFilter::create('state', [
                    'sylius.ui.cancelled' => 'cancelled',
                    'sylius.ui.ready' => 'ready',
                    'sylius.ui.shipped' => 'shipped',
                ])
                    ->setLabel('sylius.ui.state'),
            )
            ->addFilter(
                Filter::create('channel', 'ux_autocomplete')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'fields' => [
                            'order.channel.id',
                        ],
                    ])
                    ->setFormOptions([
                        'extra_options' => [
                            'class' => $this->channelClass,
                            'choice_label' => 'name',
                        ],
                    ]),
            )
            ->addFilter(
                Filter::create('method', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.method')
                    ->setOptions([
                        'fields' => [
                            'method.id',
                        ],
                    ])
                    ->setFormOptions([
                        'extra_options' => [
                            'class' => $this->shippingMethodClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'name',
                        ],
                    ]),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('ship', 'ship_with_tracking_code'),
                    ShowAction::create(),
                ),
            );
    }
}
