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
use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;

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
     * Constructor.
     *
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(ShippablesAwareInterface $shippablesAware, array $criteria = array())
    {
        $methods = array();

        foreach ($this->getMethods($criteria) as $method) {
            if ($method->supports($shippablesAware)) {
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
    protected function getMethods(array $criteria)
    {
        $criteria = array_merge(array('enabled' => true), $criteria);

        return $this->repository->findBy($criteria);
    }
}
