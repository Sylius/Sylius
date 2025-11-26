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
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Filter\SelectFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\AttributeType\FloatAttributeType;
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\PercentAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextareaAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(name: 'sylius_admin_product_attribute')]
final class ProductAttributeGrid extends AbstractGrid implements ProductAttributeGridInterface
{
    public function __construct(
        private readonly string $productAttributeClass,
    ) {
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setDriverOption('class', $this->productAttributeClass)
            ->setRepositoryMethod('createListQueryBuilder', [
                "expr:service('sylius.context.locale').getLocaleCode()",
            ])
            ->setLimits([10, 25, 50])
            ->addOrderBy('position', 'asc')

            // -- Fields
            ->addField(
                TwigField::create('name', '@SyliusAdmin/shared/grid/field/name.html.twig')
                    ->setLabel('sylius.ui.name')
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
            ->addField(
                TwigField::create('type', '@SyliusUi/grid/field/label.html.twig')
                    ->setLabel('sylius.ui.type')
                    ->setSortable(true),
            )
            ->addField(
                TwigField::create('translatable', '@SyliusAdmin/shared/grid/field/boolean.html.twig')
                    ->setLabel('sylius.ui.translatable')
                    ->setSortable(true),
            )
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

            // -- Filter
            ->addFilter(
                StringFilter::create('search', ['translations.name', 'code'])
                    ->setLabel('sylius.ui.search')
                    ->setFormOptions(['type' => StringFilter::TYPE_CONTAINS]),
            )
            ->addFilter(
                SelectFilter::create('type', [
                    'sylius.ui.checkbox' => CheckboxAttributeType::TYPE,
                    'sylius.ui.date' => DateAttributeType::TYPE,
                    'sylius.ui.datetime' => DatetimeAttributeType::TYPE,
                    'sylius.ui.float' => FloatAttributeType::TYPE,
                    'sylius.ui.integer' => IntegerAttributeType::TYPE,
                    'sylius.ui.percent' => PercentAttributeType::TYPE,
                    'sylius.ui.select' => SelectAttributeType::TYPE,
                    'sylius.ui.text' => TextAttributeType::TYPE,
                    'sylius.ui.textarea' => TextareaAttributeType::TYPE,
                ])
                    ->setLabel('sylius.ui.type')
                    ->addFormOption('multiple', true)
                    ->addFormOption('autocomplete', true),
            )
            ->addFilter(
                Filter::create('translatable', 'boolean')
                    ->setLabel('sylius.ui.translatable'),
            )

            // -- Actions
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
}
