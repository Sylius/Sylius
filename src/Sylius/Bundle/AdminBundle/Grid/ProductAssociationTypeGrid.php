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
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ProductAssociationTypeGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_association_type';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder', [
                $this->localeContext->getLocaleCode(),
            ])
            ->addOrderBy('code', 'asc')
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('name')
                    ->setLabel('sylius.ui.name')
            )
            ->addFilter(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code')
            )
            ->addFilter(
                Filter::create('name', 'string')
                    ->setLabel('sylius.ui.name')
                    ->setOptions([
                        'fields' => [
                            'translation.name',
                        ],
                    ])
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

    public function getResourceClass(): string
    {
        return $this->resourceClass;
    }
}
