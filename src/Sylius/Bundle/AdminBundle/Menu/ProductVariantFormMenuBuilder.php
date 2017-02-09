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
use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ProductVariantFormMenuBuilder
{
    const EVENT_NAME = 'sylius.menu.admin.product_variant.form';

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

        $this->eventDispatcher->dispatch(
            self::EVENT_NAME,
            new ProductVariantMenuBuilderEvent($this->factory, $menu, $options['product_variant'])
        );

        return $menu;
    }
}
