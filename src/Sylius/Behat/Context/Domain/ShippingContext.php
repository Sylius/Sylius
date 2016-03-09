<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShippingContext implements Context
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @Then /^there should be no shipments with ("[^"]+" shipping method) in the registry$/
     */
    public function shipmentShouldNotExistInTheRegistry(ShippingMethodInterface $shippingMethod)
    {
        $shippings = $this->shipmentRepository->findBy(['method' => $shippingMethod]);

        expect($shippings)->toBe([]);
    }
}
