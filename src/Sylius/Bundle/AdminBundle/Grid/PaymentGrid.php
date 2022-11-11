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
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class PaymentGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $channelResourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_payment';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder')
            ->addOrderBy('createdAt', 'desc')
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('sylius.ui.date')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                TwigField::create('number', '@SyliusAdmin/Order/Grid/Field/number.html.twig')
                    ->setLabel('sylius.ui.order')
                    ->setPath('order')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/number.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('channel', '@SyliusAdmin/Order/Grid/Field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setPath('order.channel')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/channel.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('customer', '@SyliusAdmin/Order/Grid/Field/customer.html.twig')
                    ->setLabel('sylius.ui.customer')
                    ->setPath('order.customer')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Order/Grid/Field/customer.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/Common/Label/paymentState.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Common/Label/paymentState.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('state', 'select')
                    ->setLabel('sylius.ui.state')
                    ->setFormOptions([
                        'choices' => [
                            'sylius.ui.cancelled' => 'cancelled',
                            'sylius.ui.completed' => 'completed',
                            'sylius.ui.failed' => 'failed',
                            'sylius.ui.new' => 'new',
                            'sylius.ui.processing' => 'processing',
                            'sylius.ui.refunded' => 'refunded',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('channel', 'entity')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'fields' => [
                            'order.channel',
                        ],
                    ])
                    ->setFormOptions([
                        'class' => $this->channelResourceClass,
                    ])
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('complete', 'apply_transition')
                        ->setLabel('sylius.ui.complete')
                        ->setIcon('payment')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_payment_complete',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                            'class' => 'teal',
                            'transition' => 'complete',
                            'graph' => 'sylius_payment',
                        ])
                )
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
