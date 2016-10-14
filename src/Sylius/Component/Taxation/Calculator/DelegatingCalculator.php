<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Calculator;

use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class DelegatingCalculator implements CalculatorInterface
{
    /**
     * @var ServiceRegistry
     */
    private $calculatorsRegistry;

    /**
     * @param ServiceRegistry $serviceRegistry
     */
    public function __construct(ServiceRegistry $serviceRegistry)
    {
        $this->calculatorsRegistry = $serviceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate($base, TaxRateInterface $rate)
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorsRegistry->get($rate->getCalculator());

        return $calculator->calculate($base, $rate);
    }
}
