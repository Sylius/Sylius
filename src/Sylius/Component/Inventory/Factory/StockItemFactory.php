<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Inventory\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockLocationInterface;
use Sylius\Component\Inventory\Repository\StockItemRepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockItemFactory extends Factory implements StockItemFactoryInterface
{
    /**
     * @var StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var ObjectManager
     */
    protected $stockItemManager;

    /**
     * @var RepositoryInterface
     */
    protected $stockLocationRepository;

    /**
     * @var RepositoryInterface
     */
    protected $stockableRepository;

    /**
     * Constructor.
     *
     * @param StockItemRepositoryInterface $stockItemRepository
     * @param ObjectManager                $stockItemManager
     * @param RepositoryInterface          $stockLocationRepository
     * @param RepositoryInterface          $stockableRepository
     */
    public function __construct($className,
                                StockItemRepositoryInterface $stockItemRepository,
                                ObjectManager $stockItemManager,
                                RepositoryInterface $stockLocationRepository,
                                RepositoryInterface $stockableRepository
    ) {
        parent::__construct($className);

        $this->stockItemRepository = $stockItemRepository;
        $this->stockItemManager = $stockItemManager;
        $this->stockLocationRepository = $stockLocationRepository;
        $this->stockableRepository = $stockableRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createForLocation(StockableInterface $stockable, StockLocationInterface $location)
    {
        if (null !== $item = $this->stockItemRepository->findByStockableAndLocation($stockable, $location)) {
            return $item;
        }

        $item = $this->createNew();
        $item->setStockable($stockable);
        $item->setLocation($location);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function createAllForStockable(StockableInterface $stockable)
    {
        $stockLocations = $this->stockLocationRepository->findAll();

        foreach ($stockLocations as $location) {
            $item = $this->createForLocation($stockable, $location);

            if (null === $item->getId()) {
                $this->stockItemManager->persist($item);
            }
        }

        $this->stockItemManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createAllForLocation(StockLocationInterface $location)
    {
        $stockables = $this->stockableRepository->findAll();

        foreach ($stockables as $stockable) {
            $item = $this->createForLocation($stockable, $location);

            if (null === $item->getId()) {
                $this->stockItemManager->persist($item);
            }
        }

        $this->stockItemManager->flush();
    }
}
