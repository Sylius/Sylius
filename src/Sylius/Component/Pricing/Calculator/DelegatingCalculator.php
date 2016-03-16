<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Pricing\Calculator;

use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

/**
 * This class delegates the calculation of charge for particular subject
 * to a correct calculator instance, based on the type defined on the priceable.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DelegatingCalculator implements DelegatingCalculatorInterface
{
    /**
     * Calculator registry.
     *
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $registry
     */
    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $context = [])
    {
        if (null === $type = $subject->getPricingCalculator()) {
            throw new \InvalidArgumentException('Cannot calculate the price for PriceableInterface instance without calculator defined.');
        }

        $calculator = $this->registry->get($type);

        return $calculator->calculate($subject, $subject->getPricingConfiguration(), $context);
    }
}
