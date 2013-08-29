<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Form\DataTransformer;

use Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariantInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Option values combination to variant transformer.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantToCombinationTransformer implements DataTransformerInterface
{
    /**
     * Currently matching product.
     *
     * @var VariableProductInterface
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param VariableProductInterface $product
     */
    public function __construct(VariableProductInterface $product)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return array();
        }

        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, 'Sylius\Bundle\VariableProductBundle\Model\VariantInterface');
        }

        return $value->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_array($value) && !$value instanceof \Traversable && !$value instanceof \ArrayAccess) {
            throw new UnexpectedTypeException($value, '\Traversable or \ArrayAccess');
        }

        foreach ($this->product->getVariants() as $variant) {
            $matches = true;

            foreach ($value as $option) {
                if (!$variant->hasOption($option)) {
                    $matches = false;
                }
            }

            if ($matches) {
                return $variant;
            }
        }

        return null;
    }
}
