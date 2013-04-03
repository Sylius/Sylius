<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Resolver;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ShippingBundle\Checker\ShippingMethodEliglibilityCheckerInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Default available methods resolver.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MethodsResolver implements MethodsResolverInterface
{
    /**
     * Shipping methods repository.
     *
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * Shipping method eligibility checker.
     *
     * @var ShippingMethodEliglibilityCheckerInterface
     */
    protected $eliglibilityChecker;

    /**
     * Constructor.
     *
     * @param ObjectRepository                           $repository
     * @param ShippingMethodEliglibilityCheckerInterface $eliglibilityChecker
     */
    public function __construct(ObjectRepository $repository, ShippingMethodEliglibilityCheckerInterface $eligibilityChecker)
    {
        $this->repository = $repository;
        $this->eliglibilityChecker = $eliglibilityChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(ShippingSubjectInterface $subject, array $criteria = array())
    {
        $methods = array();

        foreach ($this->getMethods($criteria) as $method) {
            if ($this->eliglibilityChecker->isEliglible($subject, $method)) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * Return all methods matching given criteria.
     *
     * @param array $criteria
     */
    protected function getMethods(array $criteria = array())
    {
        return $this->repository->findBy($criteria);
    }
}
