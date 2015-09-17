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
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MethodsResolver implements MethodsResolverInterface
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    protected $eligibilityChecker;

    /**
     * @param ObjectRepository                          $repository
     * @param ShippingMethodEligibilityCheckerInterface $eligibilityChecker
     */
    public function __construct(ObjectRepository $repository, ShippingMethodEligibilityCheckerInterface $eligibilityChecker)
    {
        $this->repository = $repository;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject, array $criteria = array())
    {
        $methods = array();

        foreach ($this->getMethods($criteria) as $method) {
            if ($this->eligibilityChecker->isEligible($subject, $method)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * @param array $criteria
     *
     * @return ShippingMethodInterface[]
     */
    protected function getMethods(array $criteria = array())
    {
        return $this->repository->findBy($criteria);
    }
}
