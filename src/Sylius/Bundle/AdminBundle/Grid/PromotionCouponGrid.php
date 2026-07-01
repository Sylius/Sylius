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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class PromotionCouponGrid implements PromotionCouponGridInterface
{
    public function __construct(
        private readonly string $promotionCouponClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->promotionCouponClass)
            ->setLimits([10, 25, 50])
            ->orderBy('used', 'desc')
            ->setRepositoryMethod('createQueryBuilderByPromotionId', [
                'promotionId' => '$promotionId',
            ])
            ->withFields(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
                StringField::create('usageLimit')
                    ->setLabel('sylius.ui.usage_limit')
                    ->setSortable(true),
                StringField::create('perCustomerUsageLimit')
                    ->setLabel('sylius.ui.per_customer_usage_limit')
                    ->setSortable(true),
                TwigField::create('used', '@SyliusAdmin/promotion_coupon/grid/field/used.html.twig')
                    ->setLabel('sylius.ui.used')
                    ->setSortable(true)
                    ->setPath('.'),
                DateTimeField::create('expiresAt')
                    ->setLabel('sylius.ui.expires_at')
                    ->setSortable(true)
                    ->setOption('format', 'd-m-Y'),
            )
            ->withFilters(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('generate', 'default')
                        ->setLabel('sylius.ui.generate')
                        ->setIcon('tabler:arrows-split')
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
                        ]),
                ),
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
                ),
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
}
