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

use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * Inventory management helper methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtension extends \Twig_Extension
{
    /**
     * Availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    private $checker;

    /**
     * Constructor.
     *
     * @param AvailabilityCheckerInterface $checker
     */
    public function __construct(AvailabilityCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_inventory_is_available' => new \Twig_Function_Method($this, 'isStockAvailable'),
            'sylius_inventory_is_sufficient' => new \Twig_Function_Method($this, 'isStockSufficient'),
        );
    }

    /**
     * Check whether stockable is in stock or not.
     *
     * @param StockableInterface $stockable
     *
     * @return Boolean
     */
    public function isStockAvailable(StockableInterface $stockable)
    {
        return $this->checker->isStockAvailable($stockable);
    }

    /**
     * Check whether stock is sufficient for given
     * stockable and quantity.
     *
     * @param StockableInterface $stockable
     * @param integer            $quantity
     *
     * @return Boolean
     */
    public function isStockSufficient(StockableInterface $stockable, $quantity)
    {
        return $this->checker->isStockSufficient($stockable, $quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory';
    }
}
