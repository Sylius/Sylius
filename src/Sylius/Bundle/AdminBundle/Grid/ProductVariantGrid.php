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
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ProductVariantGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product_variant';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createQueryBuilderByProductId', [
                $this->localeContext->getLocaleCode(),
                '$productId',
            ])
            ->addOrderBy('position', 'asc')
            ->addField(
                TwigField::create('name', '@SyliusAdmin/ProductVariant/Grid/Field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/ProductVariant/Grid/Field/name.html.twig',
                    ])
            )
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
            )
            ->addField(
                TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('inventory', '@SyliusAdmin/ProductVariant/Grid/Field/inventory.html.twig')
                    ->setLabel('sylius.ui.inventory')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/ProductVariant/Grid/Field/inventory.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('position', '@SyliusAdmin/ProductVariant/Grid/Field/position.html.twig')
                    ->setLabel('sylius.ui.position')
                    ->setPath('.')
                    ->setSortable(true, 'position')
                    ->setOptions([
                        'template' => '@SyliusAdmin/ProductVariant/Grid/Field/position.html.twig',
                    ])
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
                    Action::create('generate', 'generate_variants')
                        ->setOptions([
                            'product' => 'expr:service(\'sylius.repository.product\').find($productId)',
                        ]),
                    Action::create('update_positions', 'update_product_variant_positions'),
                    CreateAction::create()
                        ->setOptions([
                            'link' => [
                                'parameters' => [
                                    'productId' => '$productId',
                                ],
                            ],
                        ])
                ),
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    UpdateAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'productId' => '$productId',
                            ],
                        ],
                    ]),
                    DeleteAction::create([
                        'link' => [
                            'parameters' => [
                                'id' => 'resource.id',
                                'productId' => '$productId',
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
                                'productId' => '$productId',
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
