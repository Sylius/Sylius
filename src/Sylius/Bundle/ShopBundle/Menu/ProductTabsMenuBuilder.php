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

namespace Sylius\Bundle\ShopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\ShopBundle\Event\ProductMenuBuilderEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ProductTabsMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.shop.product.tabs';

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('product', $options) || !$options['product'] instanceof ProductInterface) {
            return $menu;
        }

        /** @var ProductInterface $product */
        $product = $options['product'];

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SyliusShop/Product/Show/Tabs/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        if (count($product->getAttributes()) > 0) {
            $menu
                ->addChild('attributes')
                ->setAttribute('template', '@SyliusShop/Product/Show/Tabs/_attributes.html.twig')
                ->setLabel('sylius.ui.attributes')
            ;
        }

        $menu
            ->addChild('reviews')
            ->setAttribute('template', '@SyliusShop/Product/Show/Tabs/_reviews.html.twig')
            ->setAttribute('item_count', count($product->getAcceptedReviews()))
            ->setLabel('sylius.ui.reviews_with_count')
        ;

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ProductMenuBuilderEvent($this->factory, $menu, $product)
        );

        return $menu;
    }
}
