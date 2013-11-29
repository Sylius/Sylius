<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checker;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcherInterface;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

class RestrictedZoneChecker implements RestrictedZoneCheckerInterface
{
    private $securityContext;
    private $zoneMatcher;

    public function __construct(SecurityContextInterface $securityContext, ZoneMatcherInterface $zoneMatcher)
    {
        $this->securityContext = $securityContext;
        $this->zoneMatcher = $zoneMatcher;
    }

    public function isRestricted(ProductInterface $product, AddressInterface $address = null)
    {
        if (!$this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return false;
        }

        if (null === $address && null === $address = $this->securityContext->getToken()->getUser()->getShippingAddress()) {
            return false;
        }

        if (null === $zone = $product->getRestrictedZone()) {
            return false;
        }

        return in_array($zone, $this->zoneMatcher->matchAll($address));
    }
}
