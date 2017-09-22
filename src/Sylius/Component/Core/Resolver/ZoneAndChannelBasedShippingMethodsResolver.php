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

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ZoneAndChannelBasedShippingMethodsResolver implements ShippingMethodsResolverInterface
{
    /**
     * @var ShippingMethodRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $eligibilityChecker;

    /**
     * @param ShippingMethodRepositoryInterface $shippingMethodRepository
     * @param ZoneMatcherInterface $zoneMatcher
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     */
    public function __construct(
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ZoneMatcherInterface $zoneMatcher,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->zoneMatcher = $zoneMatcher;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        // @var ShipmentInterface $subject
        Assert::true($this->supports($subject));
        /** @var OrderInterface $order */
        $order = $subject->getOrder();

        $zones = $this->zoneMatcher->matchAll($order->getShippingAddress(), Scope::SHIPPING);
        if (empty($zones)) {
            return [];
        }

        $methods = [];

        $shippingMethods = $this->shippingMethodRepository->findEnabledForZonesAndChannel($zones, $order->getChannel());
        foreach ($shippingMethods as $shippingMethod) {
            if ($this->eligibilityChecker->isEligible($subject, $shippingMethod)) {
                $methods[] = $shippingMethod;
            }
        }

        return $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject): bool
    {
        return $subject instanceof ShipmentInterface &&
            null !== $subject->getOrder() &&
            null !== $subject->getOrder()->getShippingAddress() &&
            null !== $subject->getOrder()->getChannel()
        ;
    }
}
