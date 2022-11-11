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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class PromotionGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_promotion';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('priority', 'desc')
            ->addField(
                TwigField::create('priority', '@SyliusUi/Grid/Field/position.html.twig')
                    ->setLabel('sylius.ui.priority')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/position.html.twig',
                    ])
            )
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('name', '@SyliusUi/Grid/Field/nameAndDescription.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/nameAndDescription.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('couponBased', '@SyliusUi/Grid/Field/yesNo.html.twig')
                    ->setLabel('sylius.ui.coupons')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/yesNo.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('usage', '@SyliusAdmin/Promotion/Grid/Field/usage.html.twig')
                    ->setLabel('sylius.ui.usage')
                    ->setPath('.')
                    ->setSortable(true, 'used')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Promotion/Grid/Field/usage.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('search', 'string')
                    ->setLabel('sylius.ui.search')
                    ->setOptions([
                        'fields' => [
                            'code',
                            'name',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('couponBased', 'boolean')
                    ->setLabel('sylius.ui.coupon_based')
            )
            ->addFilter(
                Filter::create('coupon_code', 'string')
                    ->setLabel('sylius.ui.coupon')
                    ->setOptions([
                        'fields' => [
                            'coupons.code',
                        ],
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('coupons', 'links')
                        ->setLabel('sylius.ui.manage_coupons')
                        ->setOptions([
                            'visible' => 'resource.couponBased',
                            'icon' => 'ticket',
                            'links' => [
                                'index' => [
                                    'label' => 'sylius.ui.list_coupons',
                                    'icon' => 'list',
                                    'route' => 'sylius_admin_promotion_coupon_index',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                                'create' => [
                                    'label' => 'sylius.ui.create',
                                    'icon' => 'plus',
                                    'route' => 'sylius_admin_promotion_coupon_create',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                                'generate' => [
                                    'label' => 'sylius.ui.generate',
                                    'icon' => 'random',
                                    'route' => 'sylius_admin_promotion_coupon_generate',
                                    'parameters' => [
                                        'promotionId' => 'resource.id',
                                    ],
                                ],
                            ],
                        ]),
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                )
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
