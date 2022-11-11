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
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class PromotionCouponGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_promotion_coupon';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createQueryBuilderByPromotionId', [
                'promotionId' => '$promotionId',
            ])
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('usageLimit')
                    ->setLabel('sylius.ui.usage_limit')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('perCustomerUsageLimit')
                    ->setLabel('sylius.ui.per_customer_usage_limit')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('used')
                    ->setLabel('sylius.ui.used')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('expiresAt')
                    ->setLabel('sylius.ui.expires_at')
                    ->setSortable(true)
                    ->setOptions([
                        'format' => 'd-m-Y',
                    ])
            )
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('generate', 'default')
                        ->setLabel('sylius.ui.generate')
                        ->setIcon('random')
                        ->setOptions([
                            'link' => [
                                'route' => 'sylius_admin_promotion_coupon_generate',
                                'parameters' => [
                                    'promotionId' => '$promotionId',
                                ],
                            ],
                        ]),
                    CreateAction::create()
                        ->setOptions([
                            'link' => [
                                'parameters' => [
                                    'promotionId' => '$promotionId',
                                ],
                            ],
                        ])
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'promotionId' => '$promotionId',
                            ],
                        ],
                    ]),
                    DeleteAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'promotionId' => '$promotionId',
                            ],
                        ],
                    ]),
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create([
                        'link' => [
                            'parameters' => [
                                'promotionId' => '$promotionId',
                            ],
                        ],
                    ]),
                ),
            );
    }

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
