<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProductMenuBuilderEvent extends MenuBuilderEvent
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param ProductInterface $product
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, ProductInterface $product)
    {
        parent::__construct($factory, $menu);

        $this->product = $product;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }
}
