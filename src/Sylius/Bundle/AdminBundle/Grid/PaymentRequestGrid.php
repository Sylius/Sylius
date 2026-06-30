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
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

#[AsGrid(name: self::NAME)]
final class PaymentRequestGrid implements PaymentRequestGridInterface
{
    public function __construct(
        private readonly string $paymentRequestClass,
        private readonly string $paymentMethodClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->setDriverOption('class', $this->paymentRequestClass);
        $gridBuilder->setRepositoryMethod('createQueryBuilderForPayment', [
            'paymentId' => '$paymentId',
        ]);
        $gridBuilder->setLimits([10, 25, 50]);
        $gridBuilder->addOrderBy('createdAt', 'desc');

        // -- Fields
        $gridBuilder
            ->addField(
                StringField::create('hash')
                    ->setLabel('sylius.ui.hash')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('method', '@SyliusAdmin/payment_request/grid/field/method.html.twig')
                    ->setLabel('sylius.ui.payment_method')
                    ->setPath('.'),
            )
            ->addField(
                TwigField::create('action', '@SyliusAdmin/payment_request/grid/field/action.html.twig')
                    ->setLabel('sylius.ui.action'),
            )
            ->addField(
                TwigField::create('state', '@SyliusAdmin/payment_request/grid/field/state.html.twig')
                    ->setLabel('sylius.ui.state'),
            )
            ->addField(
                TwigField::create('createdAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.creation_date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('updatedAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.updating_date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )

            // -- Filters
            ->addFilter(
                Filter::create('payment_method', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.payment_method')
                    ->setFormOptions(['extra_options' => [
                        'class' => $this->paymentMethodClass,
                        'translation_fields' => ['name'],
                        'choice_label' => 'name',
                    ]])
                    ->setOptions([
                        'fields' => ['method.id'],
                    ]),
            )
            ->addFilter(
                SelectFilter::create('action', [
                    'sylius.ui.authorize' => PaymentRequestInterface::ACTION_AUTHORIZE,
                    'sylius.ui.cancel' => PaymentRequestInterface::ACTION_CANCEL,
                    'sylius.ui.capture' => PaymentRequestInterface::ACTION_CAPTURE,
                    'sylius.ui.notify' => PaymentRequestInterface::ACTION_NOTIFY,
                    'sylius.ui.payout' => PaymentRequestInterface::ACTION_PAYOUT,
                    'sylius.ui.refund' => PaymentRequestInterface::ACTION_REFUND,
                    'sylius.ui.status' => PaymentRequestInterface::ACTION_STATUS,
                    'sylius.ui.sync' => PaymentRequestInterface::ACTION_SYNC,
                ])
                    ->setLabel('sylius.ui.action'),
            )
            ->addFilter(
                SelectFilter::create('state', [
                    'sylius.ui.cancelled' => PaymentRequestInterface::STATE_CANCELLED,
                    'sylius.ui.completed' => PaymentRequestInterface::STATE_COMPLETED,
                    'sylius.ui.failed' => PaymentRequestInterface::STATE_FAILED,
                    'sylius.ui.new' => PaymentRequestInterface::STATE_NEW,
                    'sylius.ui.processing' => PaymentRequestInterface::STATE_PROCESSING,
                ])
                    ->setLabel('sylius.ui.state'),
            )

            // -- Actions
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create()
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_payment_request_show',
                                'parameters' => [
                                    'hash' => 'resource.hash',
                                    'paymentId' => 'resource.payment.id',
                                ],
                            ],
                        ]),
                ),
            )
        ;
    }
}
