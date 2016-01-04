<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Provider;

use Sylius\Component\Inventory\Model\InventorySubjectInterface;
use Sylius\Component\Inventory\Repository\StockLocationRepositoryInterface;

/**
 * Default provider returns all enabled stock locations.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationProvider implements StockLocationProviderInterface
{
    /**
     * Repository for stock location.
     *
     * @var StockLocationRepositoryInterface
     */
    protected $stockLocationRepository;

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
    public function getAvailableStockLocations(InventorySubjectInterface $subject)
    {
        return $this->stockLocationRepository->findAllEnabled();
    }
}
