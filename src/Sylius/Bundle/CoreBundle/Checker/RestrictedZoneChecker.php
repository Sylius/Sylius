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

use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
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

    /**
     * {@inheritdoc}
     */
    public function isRestricted($subject, AddressInterface $address = null)
    {
        if (!$subject instanceof ProductInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\ProductInterface');
        }

        if (!$this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return false;
        }

        if (null === $address && null === $address = $this->securityContext->getToken()->getUser()->getShippingAddress()) {
            return false;
        }

        if (null === $zone = $subject->getRestrictedZone()) {
            return false;
        }

        return in_array($zone, $this->zoneMatcher->matchAll($address));
    }
}
