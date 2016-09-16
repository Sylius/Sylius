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
use Sylius\Component\Core\Inventory\Updater\OrderQuantityUpdaterInterface;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderInventoryHandler implements OrderInventoryHandlerInterface
{
    /**
     * @var OrderQuantityUpdaterInterface
     */
    private $onHandQuantityUpdater;

    /**
     * @var OrderQuantityUpdaterInterface
     */
    private $onHoldQuantityUpdater;

    /**
     * @param OrderQuantityUpdaterInterface $onHandQuantityUpdater
     * @param OrderQuantityUpdaterInterface $onHoldQuantityUpdater
     */
    public function __construct(
        OrderQuantityUpdaterInterface $onHandQuantityUpdater,
        OrderQuantityUpdaterInterface $onHoldQuantityUpdater
    ) {
        $this->onHandQuantityUpdater = $onHandQuantityUpdater;
        $this->onHoldQuantityUpdater = $onHoldQuantityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(OrderInterface $order)
    {
        try {
            $this->onHandQuantityUpdater->decrease($order);
            $this->onHoldQuantityUpdater->decrease($order);
        } catch (\InvalidArgumentException $exception) {
            throw new HandleException(self::class, 'Cannot decrease inventory on hold and on hand quantity', $exception);
        }
    }
}
