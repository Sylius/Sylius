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

use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
abstract class AbstractCalculator
{
    protected $parameterName;
    protected $className;

    public function __construct()
    {
        if (null === $this->parameterName || null === $this->className) {
            throw new \RuntimeException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(PriceableInterface $subject, array $configuration, array $context = array())
    {
        if (!array_key_exists($this->parameterName, $context)) {
            return $subject->getPrice();
        }

        $price = null;
        foreach ($context[$this->parameterName] as $object) {
            if (!in_array($this->className, class_implements($object))) {
                throw new UnexpectedTypeException($object, $this->className);
            }

            $id = $object->getId();
            if (array_key_exists($id, $configuration) && (null === $price || $configuration[$id] < $price)) {
                $price = (int) round($configuration[$id]);
            }
        }

        if (null === $price) {
            return $subject->getPrice();
        }

        return $price;
    }
}
