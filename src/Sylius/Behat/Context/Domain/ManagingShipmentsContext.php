<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Webmozart\Assert\Assert;

final class ManagingShipmentsContext implements Context
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    public function __construct(ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @Then /^there should be no shipments with ("[^"]+" shipping method) in the registry$/
     */
    public function shipmentShouldNotExistInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        $shipments = $this->shipmentRepository->findBy(['method' => $shippingMethod]);

        Assert::same($shipments, []);
    }
}
