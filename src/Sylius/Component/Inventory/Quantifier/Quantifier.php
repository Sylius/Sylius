<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Quantifier;

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * Default implementation of quantifier, which uses stock item repository to count items.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Quantifier implements QuantifierInterface
{
    /**
     * @var StockItemRepositoryInterface
     */
    private $stockItemRepository;

    /**
     * @param StockItemRepositoryInterface $stockItemRepository
     */
    public function __construct(StockItemRepositoryInterface $stockItemRepository)
    {
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalOnHand(StockableInterface $stockable)
    {
        return $this->stockItemRepository->countOnHandStockable($stockable);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalOnHold(StockableInterface $stockable)
    {
        return $this->stockItemRepository->countOnHoldByStockable($stockable);
    }
}
