<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Backorders;

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Repository\StockLocationRepositoryInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;

/**
 * Default implementation of backorders manager, who decides about availability of item.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackordersManager implements BackordersManagerInterface
{
    /**
     * @var StockLocationRepositoryInterface
     */
    private $stockLocationRepository;

    /**
     * @param StockLocationRepositoryInterface $stockLocationRepository
     */
    public function __construct(StockLocationRepositoryInterface $stockLocationRepository)
    {
        $this->stockLocationRepository = $stockLocationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackorderable(StockableInterface $stockable)
    {
        return 0 !== $this->stockLocationRepository->countBackorderableByStockable($stockable);
    }
}
