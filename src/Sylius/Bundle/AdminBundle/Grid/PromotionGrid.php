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
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\ExistsFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.promotion.class%', name: self::NAME)]
final class PromotionGrid implements PromotionGridInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 25, 50])
            ->addOrderBy('priority', 'desc')
            ->withFields(
                TwigField::create('priority', '@SyliusAdmin/promotion/grid/field/priority.html.twig')
                    ->setLabel('sylius.ui.priority')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
                TwigField::create('name', '@SyliusUi/grid/field/name_and_description.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-100',
                        ],
                    ]),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
                TwigField::create('couponBased', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.coupons')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
                TwigField::create('usage', '@SyliusAdmin/promotion/grid/field/usage.html.twig')
                    ->setLabel('sylius.ui.usage')
                    ->setPath('.')
                    ->setSortable(true, 'used'),
            )

            ->withFilters(
                StringFilter::create('search', ['code', 'name'])
                    ->setLabel('sylius.ui.search'),
                Filter::create('couponBased', 'boolean')
                    ->setLabel('sylius.ui.coupon_based'),
                Filter::create('coupon_code', 'string')
                    ->setLabel('sylius.ui.coupon')
                    ->setOptions([
                        'fields' => [
                            'coupons.code',
                        ],
                    ]),
                ExistsFilter::create('archival', 'archivedAt')
                    ->setLabel('sylius.ui.archival')
                    ->setDefaultValue(false),
            )

            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create(),
                    DeleteAction::create(),
                    Action::create('archive', 'archive'),
                ),
            )
            ->addActionGroup(
                ActionGroup::create(
                    'subitem',
                    Action::create('coupons', 'list')
                        ->setLabel('sylius.ui.manage_coupons')
                        ->setOptions([
                            'visible' => 'resource.couponBased',
                            'links' => [
                                'index' => [
                                'label' => 'sylius.ui.list_coupons',
                                'route' => 'sylius_admin_promotion_coupon_index',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                                'create' => [
                                'label' => 'sylius.ui.create',
                                'route' => 'sylius_admin_promotion_coupon_create',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                                'generate' => [
                                'label' => 'sylius.ui.generate',
                                'route' => 'sylius_admin_promotion_coupon_generate',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                            ],
                        ]),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            )
        ;
    }
}
