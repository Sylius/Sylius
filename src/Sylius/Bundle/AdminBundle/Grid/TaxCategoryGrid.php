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

#[AsGrid(name: 'sylius_admin_tax_category')]
final class TaxCategoryGrid extends AbstractGrid implements TaxCategoryGridInterface
{
    public function __construct(
        private string $taxCategoryClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->taxCategoryClass)
            ->setLimits([10, 25, 50])
            ->addOrderBy('nameAndDescription', 'asc')

            // -- Fields
            ->addField(
                TwigField::create('nameAndDescription', '@SyliusUi/grid/field/name_and_description.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setSortable(true, 'name'),
            )
            ->addField(
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true),
            )

            // -- Filters
            ->addFilter(
                StringFilter::create('search', ['code', 'name'])
                    ->setLabel('sylius.ui.search')
                    ->addFormOption('type', StringFilter::TYPE_CONTAINS),
            )

            // -- Actions
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
