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

use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class OrderGrid extends AbstractGrid
{
    public function __construct(
        private readonly string $resourceClass,
        private readonly string $channelResourceClass,
        private readonly string $customerClass,
        private readonly string $productClass,
        private readonly string $productVariantClass,
        private readonly string $shippingMethodResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_order';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->resourceClass)
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod(
                'createCriteriaAwareSearchListQueryBuilder',
                ['criteria' => '$criteria'],
            )
            ->addOrderBy('number', 'desc')
            ->addField(
                TwigField::create('number', '@SyliusAdmin/shared/grid/field/order_number.html.twig')
                    ->setLabel('sylius.ui.number')
                    ->setPath('.')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('date', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.date')
                    ->setPath('checkoutCompletedAt')
                    ->setSortable(true, 'checkoutCompletedAt')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('customer', '@SyliusAdmin/shared/grid/field/customer.html.twig')
                    ->setLabel('sylius.ui.customer')
                    ->setSortable(true, 'customer.lastName')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-100',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/shared/grid/field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setSortable(true, 'channel.code'),
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/order/grid/field/order_state.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('paymentState', '@SyliusAdmin/order/grid/field/payment_state.html.twig')
                    ->setLabel('sylius.ui.payment_state')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('shippingState', '@SyliusAdmin/order/grid/field/shipping_state.html.twig')
                    ->setLabel('sylius.ui.shipping_state')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('total', '@SyliusAdmin/order/grid/field/order_total.html.twig')
                    ->setLabel('sylius.ui.total')
                    ->setPath('.')
                    ->setSortable(true, 'total')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-end',
                            'td_class' => 'text-end',
                        ],
                    ]),
            )
            ->addField(
                StringField::create('currencyCode')
                    ->setLabel('sylius.ui.currency')
                    ->setSortable(true),
            )

            // -- Filters
            ->addFilter(
                SelectFilter::create('state', [
                    'sylius.ui.cancelled' => 'cancelled',
                    'sylius.ui.new' => 'new',
                    'sylius.ui.fulfilled' => 'fulfilled',
                ])
                    ->setLabel('sylius.ui.state'),
            )
            ->addFilter(
                Filter::create('product', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.product')
                    ->setFormOptions([
                        'multiple' => true,
                        'extra_options' => [
                            'class' => $this->productClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'name',
                        ],
                    ]),
            )
            ->addFilter(
                Filter::create('variant', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.variant')
                    ->setFormOptions([
                        'multiple' => true,
                        'extra_options' => [
                            'class' => $this->productVariantClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'descriptor',
                        ],
                    ]),
            )
            ->addFilter(
                Filter::create('number', 'string')
                    ->setLabel('sylius.ui.number')
                    ->setFormOptions([
                        'type' => StringFilter::TYPE_CONTAINS,
                    ]),
            )
            ->addFilter(
                Filter::create('customer', 'ux_autocomplete')
                    ->setLabel('sylius.ui.customer')
                    ->setFormOptions([
                        'extra_options' => [
                            'class' => $this->customerClass,
                            'choice_label' => 'fullname',
                        ],
                    ]),
            )
            ->addFilter(
                Filter::create('date', 'date')
                    ->setLabel('sylius.ui.date')
                    ->setOptions([
                        'field' => 'checkoutCompletedAt',
                        'inclusive_to' => true,
                    ]),
            )
            ->addFilter(
                Filter::create('channel', 'ux_autocomplete')
                    ->setLabel('sylius.ui.channel')
                    ->setFormOptions([
                        'extra_options' => [
                            'class' => $this->channelResourceClass,
                            'choice_label' => 'name',
                        ],
                    ]),
            )
            ->addFilter(
                Filter::create('total', 'money')
                    ->setLabel('sylius.ui.total')
                    ->setOptions([
                        'currency_field' => 'currencyCode',
                    ]),
            )
            ->addFilter(
                Filter::create('shipping_method', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.shipping_method')
                    ->setOptions([
                        'fields' => [
                            'shipments.method.id',
                        ],
                    ])
                    ->setFormOptions([
                        'extra_options' => [
                            'class' => $this->shippingMethodResourceClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'name',
                        ],
                    ]),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                ),
            );
    }
}
