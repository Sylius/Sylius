<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Pricing;

use Sylius\Component\Core\Model\GroupInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

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

        $price = null;

        foreach ($context['groups'] as $group) {
            if (!$group instanceof GroupInterface) {
                throw new UnexpectedTypeException($group, 'Sylius\Component\Core\Model\GroupInterface');
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
    public function getType()
    {
        return Calculators::GROUP_BASED;
    }
}
