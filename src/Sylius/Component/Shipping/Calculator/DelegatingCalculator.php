<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * This class delegates the calculation of charge for particular shipping subject
 * to a correct calculator instance, based on the name defined on the shipping method.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DelegatingCalculator implements DelegatingCalculatorInterface
{
    /**
     * Calculator registry.
     *
     * @var CalculatorRegistryInterface
     */
    protected $registry;

    /**
     * Constructor.
     *
     * @param CalculatorRegistryInterface $registry
     */
    public function __construct(CalculatorRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject)
    {
        if (null === $method = $subject->getMethod()) {
            throw new UndefinedShippingMethodException('Cannot calculate charge for shipping subject without defined shipping method.');
        }

        $calculator = $this->registry->getCalculator($method->getCalculator());

        return $calculator->calculate($subject, $method->getConfiguration());
    }
}
