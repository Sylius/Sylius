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
use Sylius\Bundle\GridBundle\Builder\ActionGroup\SubItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ProductGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct(
        private string $resourceClass,
        private string $channelResourceClass,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public static function getName(): string
    {
        return 'sylius_admin_product';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createListQueryBuilder', [
                $this->localeContext->getLocaleCode(),
                '$taxonId',
            ])
            ->addOrderBy('code', 'asc')
            ->addField(
                TwigField::create('image', '@SyliusAdmin/Product/Grid/Field/image.html.twig')
                    ->setLabel('sylius.ui.image')
                    ->setPath('.')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Product/Grid/Field/image.html.twig',
                    ])
            )
            ->addField(
                StringField::create('code')
                    ->setLabel('sylius.ui.code')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('name', '@SyliusAdmin/Product/Grid/Field/name.html.twig')
                    ->setLabel('sylius.ui.name')
                    ->setSortable(true, 'translation.name')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Product/Grid/Field/name.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('mainTaxon', '@SyliusAdmin/Product/Grid/Field/mainTaxon.html.twig')
                    ->setLabel('sylius.ui.main_taxon')
                    ->setOptions([
                        'template' => '@SyliusAdmin/Product/Grid/Field/mainTaxon.html.twig',
                    ])
            )
            ->addField(
                TwigField::create('enabled', '@SyliusUi/Grid/Field/enabled.html.twig')
                    ->setLabel('sylius.ui.enabled')
                    ->setOptions([
                        'template' => '@SyliusUi/Grid/Field/enabled.html.twig',
                    ])
            )
            ->addFilter(
                Filter::create('search', 'string')
                    ->setLabel('sylius.ui.search')
                    ->setOptions([
                        'fields' => [
                            'code',
                            'translation.name',
                        ],
                    ])
            )
            ->addFilter(
                Filter::create('enabled', 'boolean')
                    ->setLabel('sylius.ui.enabled')
            )
            ->addFilter(
                Filter::create('channel', 'entities')
                    ->setLabel('sylius.ui.channel')
                    ->setOptions([
                        'field' => 'channels.id',
                    ])
                    ->setFormOptions([
                        'class' => $this->channelResourceClass,
                    ])
            )
            ->addActionGroup(
                MainActionGroup::create(Action::create('create', 'links')
                    ->setLabel('sylius.ui.create')
                    ->setOptions([
                        'class' => 'primary',
                        'icon' => 'plus',
                        'header' => [
                            'icon' => 'cube',
                            'label' => 'sylius.ui.type',
                        ],
                        'links' => [
                            'simple' => [
                                'label' => 'sylius.ui.simple_product',
                                'icon' => 'plus',
                                'route' => 'sylius_admin_product_create_simple',
                            ],
                            'configurable' => [
                                'label' => 'sylius.ui.configurable_product',
                                'icon' => 'plus',
                                'route' => 'sylius_admin_product_create',
                            ],
                        ],
                    ]))
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create(),
                ),
            )
            ->addActionGroup(
                SubItemActionGroup::create(
                    Action::create('variants', 'links')
                        ->setLabel('sylius.ui.manage_variants')
                        ->setOptions([
                            'icon' => 'cubes',
                            'links' => [
                                'index' => [
                                    'label' => 'sylius.ui.list_variants',
                                    'icon' => 'list',
                                    'route' => 'sylius_admin_product_variant_index',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                                'create' => [
                                    'label' => 'sylius.ui.create',
                                    'icon' => 'plus',
                                    'route' => 'sylius_admin_product_variant_create',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                                'generate' => [
                                    'label' => 'sylius.ui.generate',
                                    'icon' => 'random',
                                    'route' => 'sylius_admin_product_variant_generate',
                                    'visible' => 'resource.hasOptions',
                                    'parameters' => [
                                        'productId' => 'resource.id',
                                    ],
                                ],
                            ],
                        ]),
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
