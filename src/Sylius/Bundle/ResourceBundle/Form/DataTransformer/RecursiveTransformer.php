<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class RecursiveTransformer implements DataTransformerInterface
{
    /**
     * @var DataTransformerInterface
     */
    private $transformer;

    /**
     * {@inheritdoc}
     */
    public function __construct(DataTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($values)
    {
        Assert::isArray($values);

        $transformedValues = [];
        foreach ($values as $value) {
            $transformedValues[] = $this->transformer->transform($value);
        }

        return $transformedValues;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($values)
    {
        Assert::isArray($values);

        $reverseTransformedValues = [];
        foreach ($values as $value) {
            $reverseTransformedValues[] = $this->transformer->reverseTransform($value);
        }

        return $reverseTransformedValues;
    }
}
