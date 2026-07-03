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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.payment.class%', name: self::NAME)]
final class PaymentGrid implements PaymentGridInterface
{
    public function __construct(
        private readonly string $channelClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder')
            ->addOrderBy('createdAt', 'desc')
            ->setLimits([10, 25, 50])
            ->withFields(
                TwigField::create('createdAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
                TwigField::create('number', '@SyliusAdmin/shared/grid/field/order_number.html.twig')
                    ->setLabel('sylius.ui.order')
                    ->setPath('order')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1',
                        ],
                    ]),
                TwigField::create('customer', '@SyliusAdmin/shared/grid/field/customer.html.twig')
                    ->setLabel('sylius.ui.customer')
                    ->setPath('order.customer')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-100',
                        ],
                    ]),
                TwigField::create('channel', '@SyliusAdmin/shared/grid/field/channel.html.twig')
                    ->setLabel('sylius.ui.channel')
                    ->setPath('order.channel'),
                TwigField::create('state', '@SyliusAdmin/shared/grid/field/payment_state.html.twig')
                    ->setLabel('sylius.ui.state')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->withFilters(
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
                    ]),
                Filter::create('channel', 'entity')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'fields' => [
                            'order.channel',
                        ],
                    ])
                    ->setFormOptions([
                        'class' => $this->channelClass,
                    ]),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('list_payment_requests', 'show')
                        ->setIcon('tabler:list-letters')
                        ->setLabel('sylius.ui.list_payment_requests')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_payment_request_index',
                                'parameters' => [
                                    'paymentId' => 'resource.id',
                                ],
                            ],
                        ]),
                    Action::create('complete', 'apply_transition')
                        ->setLabel('sylius.ui.complete')
                        ->setIcon('tabler:credit-card-pay')
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
                        ]),
                ),
            );
    }
}
