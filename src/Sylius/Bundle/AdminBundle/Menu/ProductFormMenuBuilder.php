<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\AdminBundle\Event\ProductMenuBuilderEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ProductFormMenuBuilder
{
    const EVENT_NAME = 'sylius.menu.admin.product.form';

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $options
     *
     * @return ItemInterface
     */
    public function createMenu(array $options = [])
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

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ProductMenuBuilderEvent($this->factory, $menu, $options['product'])
        );

        return $menu;
    }
}
