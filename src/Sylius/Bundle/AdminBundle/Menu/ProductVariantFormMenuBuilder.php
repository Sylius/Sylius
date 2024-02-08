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
use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ProductVariantFormMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.product_variant.form';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('product_variant', $options) || !$options['product_variant'] instanceof ProductVariantInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('taxes')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_taxes.html.twig')
            ->setLabel('sylius.ui.taxes')
        ;

        $menu
            ->addChild('inventory')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_inventory.html.twig')
            ->setLabel('sylius.ui.inventory')
        ;

        $menu
            ->addChild('channel_pricings')
            ->setAttribute('template', '@SyliusAdmin/ProductVariant/Tab/_channelPricings.html.twig')
            ->setLabel('sylius.ui.channel_pricings')
        ;

        $this->eventDispatcher->dispatch(
            new ProductVariantMenuBuilderEvent($this->factory, $menu, $options['product_variant']),
            self::EVENT_NAME,
        );

        return $menu;
    }
}
