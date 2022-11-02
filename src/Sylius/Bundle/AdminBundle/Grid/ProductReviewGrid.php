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

use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class ProductReviewGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_review';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('date', 'desc')
            ->addField(
                DateTimeField::create('date')
                    ->setLabel('sylius.ui.date')
                    ->setPath('createdAt')
                    ->setSortable(true, 'createdAt')
                    ->setOptions([
                        'format' => 'd-m-Y H:i:s',
                    ])
            )
            ->addField(
                StringField::create('title')
                    ->setLabel('sylius.ui.title')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('rating')
                    ->setLabel('sylius.ui.rating')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('status', '@SyliusUi/Grid/Field/state.html.twig')
                    ->setLabel('sylius.ui.status')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/state.html.twig',
                        'vars' => [
                            'labels' => '@SyliusAdmin/ProductReview/Label/Status',
                        ],
                    ])
            )
            ->addField(
                StringField::create('reviewSubject')
                    ->setLabel('sylius.ui.product')
            )
            ->addField(
                StringField::create('author')
                    ->setLabel('sylius.ui.customer')
            )
            ->addFilter(
                Filter::create('title', 'string')
                    ->setLabel('sylius.ui.title')
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
                    ->setLabel('sylius.ui.status')
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                    Action::create('accept', 'apply_transition')
                        ->setLabel('sylius.ui.accept')
                        ->setIcon('checkmark')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_product_review_accept',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                            'class' => 'green',
                            'transition' => 'accept',
                            'graph' => 'sylius_product_review',
                        ]),
                    Action::create('reject', 'apply_transition')
                        ->setLabel('sylius.ui.reject')
                        ->setIcon('remove')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_product_review_reject',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                            'class' => 'yellow',
                            'transition' => 'reject',
                            'graph' => 'sylius_product_review',
                        ]),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
