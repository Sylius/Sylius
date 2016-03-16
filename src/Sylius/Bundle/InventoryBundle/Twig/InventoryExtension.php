<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Twig;

use Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Inventory management helper methods.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryExtension extends \Twig_Extension
{
    /**
     * Inventory management helper methods.
     *
     * @var InventoryHelper
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param InventoryHelper $helper
     */
    public function __construct(InventoryHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
             new \Twig_SimpleFunction('sylius_inventory_is_available', [$this, 'isStockAvailable']),
             new \Twig_SimpleFunction('sylius_inventory_is_sufficient', [$this, 'isStockSufficient']),
        ];
    }

    /**
     * Check whether stockable is in stock or not.
     *
     * @param StockableInterface $stockable
     *
     * @return bool
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->helper->isStockAvailable($stockable);
    }

    /**
     * Check whether stock is sufficient for given
     * stockable and quantity.
     *
     * @param StockableInterface $stockable
     * @param int            $quantity
     *
     * @return bool
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        return $this->helper->isStockSufficient($stockable, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory';
    }
}
