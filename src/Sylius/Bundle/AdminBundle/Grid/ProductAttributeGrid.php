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

final class ProductAttributeGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_attribute';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addOrderBy('position', 'asc')
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('name')
                    ->setLabel('sylius.ui.name')
            )
            ->addField(
                StringField::create('position')
                    ->setEnabled(false)
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('type', '@SyliusUi/Grid/Field/label.html.twig')
                    ->setLabel('sylius.ui.type')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/label.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('translatable', '@SyliusUi/Grid/Field/yesNo.html.twig')
                    ->setLabel('sylius.ui.translatable')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/yesNo.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code')
            )
            ->addFilter(
                Filter::create('translatable', 'boolean')
                    ->setLabel('sylius.ui.translatable')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    Action::create('create', 'create_product_attribute'),
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

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
