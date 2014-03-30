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

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
