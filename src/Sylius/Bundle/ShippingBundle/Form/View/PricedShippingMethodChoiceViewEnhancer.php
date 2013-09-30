<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\View;

use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Enhances ChoiceView by adding shipping price
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class PricedShippingMethodChoiceViewEnhancer implements ShippingMethodChoiceViewEnhancerInterface
{
    protected $calculators;

    public function __construct(CalculatorRegistryInterface $calculators)
    {
        $this->calculators = $calculators;
    }

    /**
     * Turns ChoiceView into PricedShippingMethodChoiceView and adds calculated price data
     *
     * @param ChoiceView $view
     * @param ShippingSubjectInterface $subject
     * @return PricedShippingMethodChoiceView
     * @throws \InvalidArgumentException
     */
    public function enhanceChoiceView(ChoiceView $view, ShippingSubjectInterface $subject)
    {
        if ($view instanceof PricedShippingMethodChoiceView) {
            return $view;
        }

        $method = $view->data; /* @var $method ShippingMethodInterface */

        if (!$method instanceof ShippingMethodInterface) {
            throw new \InvalidArgumentException('ShippingMethodInterface expected');
        }

        $calculator = $this->calculators->getCalculator($method->getCalculator());
        $price = $calculator->calculate($subject, $method->getConfiguration());

        return new PricedShippingMethodChoiceView($view->data, $view->value, $view->label, $price);
    }
}