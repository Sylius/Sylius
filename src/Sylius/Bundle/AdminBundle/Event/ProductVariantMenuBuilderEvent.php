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
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProductVariantMenuBuilderEvent extends MenuBuilderEvent
{
    /**
     * @var ProductVariantInterface
     */
    private $productVariant;

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param ProductVariantInterface $productVariant
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, ProductVariantInterface $productVariant)
    {
        parent::__construct($factory, $menu);

        $this->productVariant = $productVariant;
    }

    /**
     * @return ProductVariantInterface
     */
    public function getProductVariant()
    {
        return $this->productVariant;
    }
}
