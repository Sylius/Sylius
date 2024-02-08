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

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ProductUpdateMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.product.update';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!isset($options['product'])) {
            return $menu;
        }

        $product = $options['product'];
        if (!$product instanceof ProductInterface) {
            return $menu;
        }

        $manageVariantsItem = $this->factory
            ->createItem('manage_variants')
            ->setAttribute('type', 'links')
            ->setLabel('sylius.ui.manage_variants')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $manageVariantsItem
            ->addChild('product_variant_index', [
                'route' => 'sylius_admin_product_variant_index',
                'routeParameters' => ['productId' => $product->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.list_variants')
            ->setLabelAttribute('icon', 'list')
        ;
        $manageVariantsItem
            ->addChild('product_variant_create', [
                'route' => 'sylius_admin_product_variant_create',
                'routeParameters' => ['productId' => $product->getId()],
            ])
            ->setAttribute('type', 'link')
            ->setLabel('sylius.ui.create')
            ->setLabelAttribute('icon', 'plus')
        ;

        if ($product->hasOptions()) {
            $manageVariantsItem
                ->addChild('product_variant_generate', [
                    'route' => 'sylius_admin_product_variant_generate',
                    'routeParameters' => ['productId' => $product->getId()],
                ])
                ->setAttribute('type', 'link')
                ->setLabel('sylius.ui.generate')
                ->setLabelAttribute('icon', 'random')
            ;
        }

        $menu->addChild($manageVariantsItem);

        $this->eventDispatcher->dispatch(
            new ProductMenuBuilderEvent($this->factory, $menu, $product),
            self::EVENT_NAME,
        );

        return $menu;
    }
}
