<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Menu\Backend;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\MenuBuilder;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Product menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductMenuBuilder extends ContainerAware
{
    const NAME = 'sylius.backend.product';

    /**
     * Builds product menu.
     *
     * @param FactoryInterface $factory
     *
     * @return ItemInterface
     */
    public function createMenu(FactoryInterface $factory, array $options)
    {
        $product = $options['product'];
        $id = $product->getId();

        $menu = $factory
            ->createItem('root')
            ->setChildrenAttribute('class', 'nav')
        ;

        $menu
            ->addChild('details', array('uri' => '#details'))
            ->setCurrent(true)
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.details')
        ;
        $menu
            ->addChild('attributes', array('uri' => '#attributes'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.attributes')
        ;
        $menu
            ->addChild('seo', array('uri' => '#seo'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.seo')
        ;
        $menu
            ->addChild('media', array('uri' => '#media'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.media')
        ;
        $menu
            ->addChild('media', array('uri' => '#media'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.media')
        ;
        $menu
            ->addChild('pricing', array('uri' => '#pricing'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.pricing')
        ;
        $menu
            ->addChild('inventory', array('uri' => '#inventory'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.inventory')
        ;
        $menu
            ->addChild('variants', array('uri' => '#variants'))
            ->setLinkAttribute('data-toggle', 'tab')
            ->setLabel('sylius.backend.product.variants')
        ;

        return $menu;
    }
}
