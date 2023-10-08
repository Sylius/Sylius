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

namespace Sylius\Bundle\ShopBundle\Grid\Account;

use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

final class OrderGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private CustomerContextInterface $customerContext,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_shop_account_order';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createByCustomerAndChannelIdQueryBuilder', [
                $this->customerContext->getCustomer()?->getId(),
                $this->channelContext->getChannel()->getId(),
            ])
            ->orderBy('checkoutCompletedAt', 'desc')
            ->addField(
                TwigField::create('number', '@SyliusShop/Account/Order/Grid/Field/number.html.twig')
                    ->setLabel('sylius.ui.number')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('checkoutCompletedAt', 'm/d/Y')
                    ->setLabel('sylius.ui.date')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('shippingAddress', '@SyliusShop/Account/Order/Grid/Field/address.html.twig')
                    ->setLabel('sylius.ui.ship_to')
            )
            ->addField(
                TwigField::create('total', '@SyliusShop/Account/Order/Grid/Field/total.html.twig')
                    ->setLabel('sylius.ui.total')
                    ->setPath('.')
                    ->setSortable(true, 'total')
            )
            ->addField(
                TwigField::create('state', '@SyliusUi/Grid/Field/label.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setSortable(true)
                    ->addOptions([
                        'vars' => [
                            'labels' => '@SyliusShop/Account/Order/Label/State',
                        ],
                    ])
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create([
                        'link' => [
                            'route' => 'sylius_shop_account_order_show',
                            'parameters' => [
                                'number' => 'resource.number',
                            ],
                        ],
                    ]),
                    Action::create('pay', 'pay')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_shop_order_show',
                                'parameters' => [
                                    'tokenValue' => 'resource.tokenvalue',
                                ],
                            ],
                        ])
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
