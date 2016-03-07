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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShippingContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @param RepositoryInterface $shipmentRepository
     */
    public function __construct(RepositoryInterface $shipmentRepository)
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
