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

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: 'sylius_admin_product_option')]
final class ProductOptionGrid extends AbstractGrid implements ProductOptionGridInterface
{
    public function __construct(
        private readonly string $productOptionClass,
        private readonly string $locale,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productOptionClass)
            ->setLimits([10, 25, 50])
            ->setRepositoryMethod('createListQueryBuilder', [
                $this->locale,
            ])
            ->addOrderBy('position', 'asc')
            ->addField(
                TwigField::create('position', '@SyliusUi/grid/field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-1 text-center',
                            'td_class' => 'text-center',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true, 'translation.name')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-75',
                        ],
                    ]),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-25',
                        ],
                    ]),
            )
            ->addFilter(
                StringFilter::create('search', ['code', 'translation.name'])
                    ->setLabel('sylius.ui.search')
                    ->addFormOption('type', StringFilter::TYPE_CONTAINS),
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
                ),
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create(),
                ),
            );
    }
}
