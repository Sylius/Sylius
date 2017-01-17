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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CollectionToStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param string $delimiter
     */
    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($values)
    {
        $expectedType = Collection::class;
        if (!($values instanceof $expectedType)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    $expectedType,
                    is_object($values) ? get_class($values) : gettype($values)
                )
            );
        }

        if ($values->isEmpty()) {
            return '';
        }

        return implode($this->delimiter, $values->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (!is_string($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected string, but got "%s"',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ('' === $value) {
            return new ArrayCollection();
        }

        return new ArrayCollection(explode($this->delimiter, $value));
    }
}
