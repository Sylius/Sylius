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
use Sylius\Component\Core\Model\UserAwareInterface;
use Sylius\Component\Core\Model\UserInterface;
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
        if (null === $address) {
            return false;
        }

        if (!$subject instanceof ProductInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Core\Model\ProductInterface');
        }

        if (null === $address = $this->getUserAddresss()) {
            return false;
        }

        if (null === $zone = $subject->getRestrictedZone()) {
            return false;
        }

        return in_array($zone, $this->zoneMatcher->matchAll($address));
    }

    /**
     * @return null|AddressInterface
     */
    private function getUserAddresss()
    {
        if ($this->securityContext->isGranted('IS_CUSTOMER') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->securityContext->getToken()->getUser();
        } else {
            return null;
        }

        if (!$user instanceof UserAwareInterface && !$user instanceof UserInterface) {
            return null;
        } elseif ($user instanceof UserAwareInterface) {
            $user = $user->getUser();
        }

        if (null === $address = $user->getShippingAddress()) {
            return null;
        }

        return $address;
    }
}
