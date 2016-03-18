<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShippingMethodContext implements Context
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     */
    public function __construct(ShippingMethodRepositoryInterface $shippingMethodRepository)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    /**
     * @Transform /^"([^"]+)" shipping method$/
     * @Transform /^shipping method "([^"]+)"$/
     * @Transform :shippingMethod
     */
    public function getShippingMethodByName($shippingMethodName)
    {
        $shippingMethod = $this->shippingMethodRepository->findOneByName($shippingMethodName);
        if (null === $shippingMethod) {
            throw new \Exception('Shipping method with name "'.$shippingMethodName.'" does not exist');
        }

        return $shippingMethod;
    }
}
