<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Shipment;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function chooseStateToFilter(string $shipmentState): void;

    public function chooseChannelFilter(string $channelName): void;

    public function chooseShippingMethodFilter(string $shippingMethodName): void;

    public function isShipmentWithOrderNumberInPosition(string $orderNumber, int $position): bool;

    public function shipShipmentOfOrderWithNumber(string $orderNumber): void;

    public function getShipmentStatusByOrderNumber(string $orderNumber): string;

    public function showOrderPageForNthShipment(int $position): void;

    public function shipShipmentOfOrderWithTrackingCode(string $orderNumber, string $trackingCode): void;

    public function getShippedAtDate(string $orderNumber): string;
}
