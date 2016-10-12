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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Antoine Goutenoir <antoine@goutenoir.com>
 */
final class VolumeBasedCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = [])
    {
        $quantity = array_key_exists('quantity', $context) ? $context['quantity'] : 1;

        foreach ($configuration as $range) {
            if (null === $range['price']) {
                throw new \Exception('Volume-based price ranges require a `price`.');
            }

            if (
                // Given that undefined minimum is assumed to be 1,
                (empty($range['min']) && $quantity <= $range['max']) ||
                // and that undefined maximum is assumed to be infinite,
                ($range['min'] <= $quantity && empty($range['max'])) ||
                // are we in this price range ?
                ($range['min'] <= $quantity && $quantity <= $range['max'])
            ) {
                return $range['price'];
            }
        }

        return $subject->getPrice();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Calculators::VOLUME_BASED;
    }
}
