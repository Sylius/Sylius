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
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: self::NAME)]
final class ProductAssociationTypeGrid implements ProductAssociationTypeGridInterface
{
    public function __construct(
        private readonly string $productAssociationTypeClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productAssociationTypeClass)
            ->setRepositoryMethod('createListQueryBuilder', [
                'expr:service(\'sylius.context.locale\').getLocaleCode()',
            ])
            ->addOrderBy('code', 'asc')
            ->setLimits([10, 25, 50])

            ->withFields(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-75',
                        ],
                    ]),
                TwigField::create('code', '@SyliusAdmin/shared/grid/field/code.html.twig')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
                    ->withOptions([
                        'vars' => [
                            'th_class' => 'w-25',
                        ],
                    ]),
            )
            ->withFilters(
                Filter::create('code', 'string')
                    ->setLabel('sylius.ui.code'),
                Filter::create('name', 'string')
                    ->setLabel('sylius.ui.name')
                    ->setOptions([
                        'fields' => [
                            'translation.name',
                        ],
                    ]),
            )
            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                UpdateAction::create(),
                DeleteAction::create(),
            )
            ->withBulkActions(
                DeleteAction::create(),
            )
        ;
    }
}
