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
     * Full inventory tracking enabled?
     *
     * @var Boolean
     */
    protected $tracking;

    /**
     * Constructor.
     *
     * @param Boolean $tracking
     */
    public function __construct($tracking = true)
    {
        $this->tracking = (Boolean) $tracking;
    }

    /**
     * {@inheritdoc}
     */
    public function isInStock(StockableInterface $stockable)
    {
        if ($this->tracking) {
            return 0 < $stockable->getOnHand();
        }

        return true;
    }
}
