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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class ProductReviewGrid implements ProductReviewGridInterface
{
    public function __construct(
        private readonly string $productReviewClass,
        private readonly string $productClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productReviewClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('date', 'desc')
            ->addField(
                TwigField::create('rating', '@SyliusAdmin/product_review/grid/field/rating.html.twig')
                    ->setLabel('sylius.ui.rating')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('author', '@SyliusAdmin/shared/grid/field/customer.html.twig')
                    ->setLabel('sylius.ui.customer'),
            )
            ->addField(
                StringField::create('title')
                    ->setLabel('sylius.ui.title')
                    ->setSortable(true),
            )
            ->addField(
                StringField::create('reviewSubject')
                    ->setLabel('sylius.ui.product'),
            )
            ->addField(
                TwigField::create('date', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.date')
                    ->setPath('createdAt')
                    ->setSortable(true, 'createdAt')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('status', '@SyliusAdmin/product_review/grid/field/status.html.twig')
                    ->setLabel('sylius.ui.status')
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
                Filter::create('title', 'string')
                    ->setLabel('sylius.ui.title'),
            )
            ->addFilter(
                SelectFilter::create(
                    'status',
                    [
                        'sylius.ui.new' => 'new',
                        'sylius.ui.accepted' => 'accepted',
                        'sylius.ui.rejected' => 'rejected',
                    ],
                )
                    ->setLabel('sylius.ui.status'),
            )
            ->addFilter(
                Filter::create('product', 'ux_translatable_autocomplete')
                    ->setLabel('sylius.ui.product')
                    ->setFormOptions([
                        'multiple' => false,
                        'extra_options' => [
                            'class' => $this->productClass,
                            'translation_fields' => ['name'],
                            'choice_label' => 'name',
                        ],
                    ])
                    ->addOption('fields', ['reviewSubject.id']),
            )

            // -- Actions
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('accept', 'apply_transition')
                        ->setLabel('sylius.ui.accept')
                        ->setIcon('tabler:check')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_product_review_accept',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                            'class' => 'btn-success btn-icon',
                            'show_disabled' => false,
                            'transition' => 'accept',
                            'graph' => 'sylius_product_review',
                        ]),
                    Action::create('reject', 'apply_transition')
                        ->setLabel('sylius.ui.reject')
                        ->setIcon('tabler:x')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_product_review_reject',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                            'class' => 'btn-warning btn-icon',
                            'show_disabled' => false,
                            'transition' => 'reject',
                            'graph' => 'sylius_product_review',
                        ]),
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
