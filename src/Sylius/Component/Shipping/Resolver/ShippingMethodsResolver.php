<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Resolver;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShippingMethodsResolver implements ShippingMethodsResolverInterface
{
    /**
     * @var ObjectRepository
     */
    private $shippingMethodRepository;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $eligibilityChecker;

    /**
     * @param ObjectRepository $shippingMethodRepository
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     */
    public function __construct(
        ObjectRepository $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject)
    {
        $methods = [];

        foreach ($this->shippingMethodRepository->findBy(['enabled' => true]) as $shippingMethod) {
            if ($this->eligibilityChecker->isEligible($subject, $shippingMethod)) {
                $methods[] = $shippingMethod;
            }
        }

        return $methods;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject)
    {
        return true;
    }
}
