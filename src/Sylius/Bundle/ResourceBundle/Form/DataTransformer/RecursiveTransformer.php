<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class RecursiveTransformer implements DataTransformerInterface
{
    /**
     * @var DataTransformerInterface
     */
    private $decoratedTransformer;

    public function __construct(DataTransformerInterface $decoratedTransformer)
    {
        $this->decoratedTransformer = $decoratedTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($values): Collection
    {
        if (null === $values) {
            return new ArrayCollection();
        }

        $this->assertTransformationValueType($values, Collection::class);

        return $values->map(function ($value) {
            return $this->decoratedTransformer->transform($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($values): Collection
    {
        if (null === $values) {
            return new ArrayCollection();
        }

        $this->assertTransformationValueType($values, Collection::class);

        return $values->map(function ($value) {
            return $this->decoratedTransformer->reverseTransform($value);
        });
    }

    /**
     * @throws TransformationFailedException
     */
    private function assertTransformationValueType($value, string $expectedType): void
    {
        if (!($value instanceof $expectedType)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    $expectedType,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }
    }
}
