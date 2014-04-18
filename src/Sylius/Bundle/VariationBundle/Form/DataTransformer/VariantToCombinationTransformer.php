<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Form\DataTransformer;

use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
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
     * Currently matching variable.
     *
     * @var VariableInterface
     */
    protected $variable;

    /**
     * Constructor.
     *
     * @param VariableInterface $variable
     */
    public function __construct(VariableInterface $variable)
    {
        $this->variable = $variable;
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
            throw new UnexpectedTypeException($value, 'Sylius\Component\Variable\Model\VariantInterface');
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

        return $this->matches($value);
    }

    /**
     * @param OptionValueInterface[] $value
     *
     * @return null|VariantInterface
     */
    private function matches($value)
    {
        foreach ($this->variable->getVariants() as $variant) {
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
