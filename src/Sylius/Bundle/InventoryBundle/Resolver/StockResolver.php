<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Resolver;

use Sylius\Bundle\InventoryBundle\Model\StockableInterface;

/**
 * Default stock resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewkski@diweb.pl>
 */
class StockResolver implements StockResolverInterface
{
    /**
     * Backorders enabled?
     *
     * @var Boolean
     */
    protected $backorders;

    /**
     * Constructor.
     *
     * @param Boolean $backorders
     */
    public function __construct($backorders)
    {
        $this->backorders = (Boolean) $backorders;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock(StockableInterface $stockable)
    {
        if (false === $this->backorders) {
            return 0 < $stockable->getOnHand();
        }

        return true;
    }
}
