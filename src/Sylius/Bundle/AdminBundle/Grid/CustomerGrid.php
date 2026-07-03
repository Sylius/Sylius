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
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(resourceClass: '%sylius.model.customer.class%', name: self::NAME)]
final class CustomerGrid implements CustomerGridInterface
{
    public function __construct(
        private readonly string $customerGroupClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setLimits([10, 25, 50])
            ->addOrderBy('createdAt', 'desc')
            ->withFields(
                TwigField::create('email', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.email')
                    ->setSortable(true),
                StringField::create('lastName')
                    ->setLabel('sylius.ui.last_name')
                    ->setSortable(true),
                StringField::create('firstName')
                    ->setLabel('sylius.ui.first_name')
                    ->setSortable(true),
                TwigField::create('createdAt', '@SyliusAdmin/shared/grid/field/date.html.twig')
                    ->setLabel('sylius.ui.registration_date')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
                TwigField::create('enabled', '@SyliusAdmin/customer/grid/field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setPath('.')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
                TwigField::create('verified', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.verified')
                    ->setPath('user?.verified')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                        ],
                    ]),
            )
            ->withFilters(
                StringFilter::create('search', ['email', 'firstName', 'lastName'])
                    ->setLabel('sylius.ui.search'),
                Filter::create('group', 'ux_autocomplete')
                ->setLabel('sylius.ui.customer_groups')
                ->setFormOptions([
                    'multiple' => true,
                    'extra_options' => [
                        'class' => $this->customerGroupClass,
                        'choice_label' => 'name',
                    ],
                ])
                ->setOptions([
                    'fields' => ['group.id'],
                ]),
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('show_orders', 'show')
                    ->setLabel('sylius.ui.show_orders')
                    ->setIcon('tabler:shopping-bag')
                    ->setOptions([
                        'link' => [
                            'route' => 'sylius_admin_customer_order_index',
                            'parameters' => ['id' => 'resource.id'],
                        ],
                    ]),
                    ShowAction::create(),
                    UpdateAction::create(),
                ),
            );
    }
}
