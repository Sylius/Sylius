<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Sorter;


use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

/**
 * Sorts shipping methods based on their price
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class PricedShippingMethodSorter implements ShippingMethodSorterInterface
{
    protected $calculators;
    protected $shippingSubject;

    public function __construct(CalculatorRegistryInterface $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(array $methods, ShippingSubjectInterface $subject)
    {
        $sorter = $this;

        usort($methods, function (ShippingMethodInterface $methodA, ShippingMethodInterface $methodB) use ($subject, $sorter) {

            $priceA = $sorter->getPrice($subject, $methodA);
            $priceB = $sorter->getPrice($subject, $methodB);

            if ($priceA === $priceB) {
                return 0;
            }

            return $priceA > $priceB ? 1 : -1;
        });

        return $methods;
    }

    public function getPrice(ShippingSubjectInterface $subject, ShippingMethodInterface $method)
    {
        $calculator = $this->calculators->getCalculator($method->getCalculator());

        return $calculator->calculate($subject, $method->getConfiguration());
    }
}