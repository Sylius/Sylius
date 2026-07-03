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

namespace Sylius\Bundle\ShopBundle\Grid\Account;

use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class OrderGrid implements OrderGridInterface
{
    public function __construct(
        private readonly string $orderClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createByCustomerAndChannelIdQueryBuilder', [
                "expr:service('sylius.context.customer').getCustomer().getId()",
                "expr:service('sylius.context.channel').getChannel().getId()",
            ])
            ->setDriverOption('class', $this->orderClass)
            ->orderBy('checkoutCompletedAt', 'desc')
            ->setLimits([10, 25, 50])
            ->withFields(
                TwigField::create('number', '@SyliusShop/account/order/grid/field/number.html.twig')
                    ->setLabel('sylius.ui.number')
                    ->setSortable(true),
                DateTimeField::create('checkoutCompletedAt', 'm/d/Y')
                    ->setLabel('sylius.ui.date'),
                TwigField::create('shippingAddress', '@SyliusShop/account/order/grid/field/address.html.twig')
                    ->setLabel('sylius.ui.ship_to'),
                TwigField::create('total', '@SyliusShop/account/order/grid/field/total.html.twig')
                    ->setLabel('sylius.ui.total')
                    ->setPath('.')
                    ->setSortable(true, 'total'),
                TwigField::create('state', '@SyliusUi/grid/field/label.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setSortable(true)
                    ->addOptions([
                        'vars' => [
                            'labels' => '@SyliusShop/account/order/label/state',
                        ],
                    ]),
            )
            ->withItemActions(
                Action::create('show', 'shop_show')
                    ->setLabel('sylius.ui.show')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_shop_account_order_show',
                            'parameters' => [
                                'number' => 'resource.number',
                            ],
                        ],
                    ]),
                Action::create('pay', 'shop_pay')
                    ->setLabel('sylius.ui.pay')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_shop_order_show',
                            'parameters' => [
                                'tokenValue' => 'resource.tokenValue',
                            ],
                        ],
                    ]),
            );
    }
}
