<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Inventory\Updater\DecreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Inventory\Updater\IncreasingQuantityUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaidOrderInventoryHandler implements PaidOrderInventoryHandlerInterface
{
    /**
     * @var PriorityQueue|DecreasingQuantityUpdaterInterface[]
     */
    private $decreasingQuantityUpdaters;

    /**
     * PaidOrderInventoryHandler constructor.
     */
    public function __construct()
    {
        $this->decreasingQuantityUpdaters = new PriorityQueue();
    }

    /**
     * @param DecreasingQuantityUpdaterInterface $decreasingQuantityUpdater
     * @param int $priority
     */
    public function addDecreasingQuantityUpdater(
        DecreasingQuantityUpdaterInterface $decreasingQuantityUpdater,
        $priority = 0
    ) {
        $this->decreasingQuantityUpdaters->insert($decreasingQuantityUpdater, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(OrderInterface $order)
    {
        if ($this->decreasingQuantityUpdaters->isEmpty()) {
            throw new HandleException(self::class, 'There is no decreasing updaters to handle this request.');
        }

        foreach ($this->decreasingQuantityUpdaters as $decreasingQuantityUpdater) {
            try {
                $decreasingQuantityUpdater->decrease($order);
            } catch (\InvalidArgumentException $exception) {
                throw new HandleException(self::class, sprintf('"%s" Decreasing updater fails.', get_class($decreasingQuantityUpdater)));
            }
        }
    }
}
