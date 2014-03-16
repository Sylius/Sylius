<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Pricing;

use Sylius\Bundle\PricingBundle\Calculator\CalculatorInterface;
use Sylius\Bundle\PricingBundle\Model\PriceableInterface;

/**
 * Customer group based calculator.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupBasedCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        if (!array_key_exists('groups', $context)) {
            return $subject->getPrice();
        }

        $groups = $context['groups'];
        $price = null;

        foreach ($groups as $group) {
            if ($group instanceof GroupInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Pricing context variable "group" must be instance of "Sylius\Bundle\CoreBundle\Model\GroupInterface", "%s" given.',
                    gettype($group)
                ));
            }

            $id = $group->getId();

            if (array_key_exists($id, $configuration)) {
                if (null === $price || $configuration[$id] < $price) {
                    $price = $configuration[$id];
                }
            }
        }

        if (null === $price) {
            return $subject->getPrice();
        }

        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_price_calculator_group_based';
    }
}
