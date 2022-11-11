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

use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class OrderGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $channelResourceClass,
        private string $shippingMethodResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_order';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createSearchListQueryBuilder')
            ->addOrderBy('number', 'desc')
            ->addField(
                DateTimeField::create('date')
                    ->setLabel('sylius.ui.date')
                    ->setPath('checkoutCompletedAt')
                    ->setSortable(true, 'checkoutCompletedAt')
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                TwigField::create('number', '@SyliusAdmin/Order/Grid/Field/number.html.twig')
                    ->setLabel('sylius.ui.number')
                    ->setPath('.')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/number.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/Order/Grid/Field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setSortable(true, 'channel.code')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/channel.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('customer', '@SyliusAdmin/Order/Grid/Field/customer.html.twig')
                    ->setLabel('sylius.ui.customer')
                    ->setSortable(true, 'customer.lastName')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/customer.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('state', '@SyliusUi/Grid/Field/state.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/state.html.twig',
                        'vars' => [
                            'labels' => '@SyliusAdmin/Order/Label/State',
                        ],
                    ])
            )
            ->addField(
                TwigField::create('paymentState', '@SyliusUi/Grid/Field/state.html.twig')
                    ->setLabel('sylius.ui.payment_state')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/state.html.twig',
                        'vars' => [
                            'labels' => '@SyliusAdmin/Order/Label/PaymentState',
                        ],
                    ])
            )
            ->addField(
                TwigField::create('shippingState', '@SyliusUi/Grid/Field/state.html.twig')
                    ->setLabel('sylius.ui.shipping_state')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/state.html.twig',
                        'vars' => [
                            'labels' => '@SyliusAdmin/Order/Label/ShippingState',
                        ],
                    ])
            )
            ->addField(
                TwigField::create('total', '@SyliusAdmin/Order/Grid/Field/total.html.twig')
                    ->setLabel('sylius.ui.total')
                    ->setPath('.')
                    ->setSortable(true, 'total')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/total.html.twig',
                    ])
            )
            ->addField(
                StringField::create('currencyCode')
                    ->setLabel('sylius.ui.currency')
                    ->setSortable(true)
            )
            ->addFilter(
                Filter::create('product', 'resource_autocomplete')
                    ->setLabel('sylius.ui.product')
                    ->setOptions([
                        'fields' => [
                            'product.code',
                        ],
                    ])
                    ->setFormOptions([
                        'resource' => 'sylius.product',
                        'choice_name' => 'descriptor',
                        'choice_value' => 'code',
                        'multiple' => true,
                        'remote_path' => 'sylius_admin_ajax_products_by_phrase',
                        'load_edit_path' => 'sylius_admin_ajax_product_by_code',
                    ])
            )
            ->addFilter(
                Filter::create('variant', 'resource_autocomplete')
                    ->setLabel('sylius.ui.variant')
                    ->setOptions([
                        'fields' => [
                            'variant.code',
                        ],
                    ])
                    ->setFormOptions([
                        'resource' => 'sylius.product_variant',
                        'choice_name' => 'descriptor',
                        'choice_value' => 'code',
                        'multiple' => true,
                        'remote_path' => 'sylius_admin_ajax_all_product_variants_by_phrase',
                        'load_edit_path' => 'sylius_admin_ajax_all_product_variants_by_codes',
                    ])
            )
            ->addFilter(
                Filter::create('number', 'string')
                    ->setLabel('sylius.ui.number')
            )
            ->addFilter(
                Filter::create('customer', 'string')
                    ->setLabel('sylius.ui.customer')
                    ->setOptions([
                        'fields' => [
                            'customer.email',
                            'customer.firstName',
                            'customer.lastName',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('date', 'date')
                    ->setLabel('sylius.ui.date')
                    ->setOptions([
                        'field' => 'checkoutCompletedAt',
                        'inclusive_to' => true,
                    ])
            )
            ->addFilter(
                Filter::create('channel', 'entity')
                    ->setLabel('sylius.ui.channel')
                    ->setFormOptions([
                        'class' => $this->channelResourceClass,
                    ])
            )
            ->addFilter(
                Filter::create('total', 'money')
                    ->setLabel('sylius.ui.total')
                    ->setOptions([
                        'currency_field' => 'currencyCode',
                    ])
            )
            ->addFilter(
                Filter::create('shipping_method', 'entity')
                    ->setLabel('sylius.ui.shipping_method')
                    ->setOptions([
                        'fields' => [
                            'shipments.method',
                        ],
                    ])
                    ->setFormOptions([
                        'class' => $this->shippingMethodResourceClass,
                    ])
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
