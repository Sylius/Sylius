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

use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    final public function calculate(PriceableInterface $subject, array $configuration, array $context = [])
    {
        $price = $this->getPriceForConfigurationAndContext($configuration, $context);

        if (null === $price) {
            return $subject->getPrice();
        }

        return $price;
    }

    /**
     * @return string
     */
    abstract protected function getParameterName();

    /**
     * @return string
     */
    abstract protected function getClassName();

    /**
     * @param array $configuration
     * @param array $context
     *
     * @return int|null
     */
    private function getPriceForConfigurationAndContext(array $configuration, array $context)
    {
        if (!array_key_exists($this->getParameterName(), $context)) {
            return null;
        }

        $price = null;
        foreach ($context[$this->getParameterName()] as $object) {
            Assert::isInstanceOf($object, $this->getClassName());

            $id = $object->getId();
            if (array_key_exists($id, $configuration) && (null === $price || $configuration[$id] < $price)) {
                $price = (int) round($configuration[$id]);
            }
        }

        return $price;
    }
}
