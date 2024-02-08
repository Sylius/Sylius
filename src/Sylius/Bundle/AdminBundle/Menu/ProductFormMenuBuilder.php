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

final class ProductFormMenuBuilder
{
    public const EVENT_NAME = 'sylius.menu.admin.product.form';

    public function __construct(private FactoryInterface $factory, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function createMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        if (!array_key_exists('product', $options) || !$options['product'] instanceof ProductInterface) {
            return $menu;
        }

        $menu
            ->addChild('details')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_details.html.twig')
            ->setLabel('sylius.ui.details')
            ->setCurrent(true)
        ;

        $menu
            ->addChild('taxonomy')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_taxonomy.html.twig')
            ->setLabel('sylius.ui.taxonomy')
        ;

        $menu
            ->addChild('attributes')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_attributes.html.twig')
            ->setLabel('sylius.ui.attributes')
        ;

        $menu
            ->addChild('associations')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_associations.html.twig')
            ->setLabel('sylius.ui.associations')
        ;

        $menu
            ->addChild('media')
            ->setAttribute('template', '@SyliusAdmin/Product/Tab/_media.html.twig')
            ->setLabel('sylius.ui.media')
        ;

        if ($options['product']->isSimple()) {
            $menu
                ->addChild('inventory')
                ->setAttribute('template', '@SyliusAdmin/Product/Tab/_inventory.html.twig')
                ->setLabel('sylius.ui.inventory')
            ;
        }

        $this->eventDispatcher->dispatch(
            new ProductMenuBuilderEvent($this->factory, $menu, $options['product']),
            self::EVENT_NAME,
        );

        return $menu;
    }
}
