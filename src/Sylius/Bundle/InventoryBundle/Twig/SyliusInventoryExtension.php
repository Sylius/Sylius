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

use Sylius\Bundle\InventoryBundle\Model\StockableInterface;
use Sylius\Bundle\InventoryBundle\Resolver\StockResolverInterface;
use Twig_Extension;
use Twig_Function_Method;

/**
 * Inventory management helper methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusInventoryExtension extends Twig_Extension
{
    /**
     * Stock resolver.
     *
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * Constructor.
     *
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(StockResolverInterface $stockResolver)
    {
        $this->stockResolver = $stockResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'sylius_inventory_in_stock' => new Twig_Function_Method($this, 'isInStock'),
        );
    }

    /**
     * Check wether stockable is in stock or not.
     *
     * @param StockableInterface $stockable
     *
     * @return Boolean
     */
    public function isInStock(StockableInterface $stockable)
    {
        return $this->stockResolver->isInStock($stockable);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory';
    }
}
