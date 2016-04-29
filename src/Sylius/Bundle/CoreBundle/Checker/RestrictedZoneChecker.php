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
use Sylius\Component\User\Context\CustomerContextInterface;

class RestrictedZoneChecker implements RestrictedZoneCheckerInterface
{
    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @param CustomerContextInterface $customerContext
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(CustomerContextInterface $customerContext, ZoneMatcherInterface $zoneMatcher)
    {
        $this->customerContext = $customerContext;
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isRestricted($subject, AddressInterface $address = null)
    {
        if (!$subject instanceof ProductInterface) {
            throw new UnexpectedTypeException($subject, ProductInterface::class);
        }

        if (null === $customer = $this->customerContext->getCustomer()) {
            return false;
        }

        if (null === $address && null === $address = $customer->getShippingAddress()) {
            return false;
        }

        if (null === $zone = $subject->getRestrictedZone()) {
            return false;
        }

        return in_array($zone, $this->zoneMatcher->matchAll($address));
    }
}
