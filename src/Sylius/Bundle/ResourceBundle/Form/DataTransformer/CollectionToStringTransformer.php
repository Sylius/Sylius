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

final class CollectionToStringTransformer implements DataTransformerInterface
{
    /** @var string */
    private $delimiter;

    public function __construct(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($values): string
    {
        if (!($values instanceof Collection)) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected "%s", but got "%s"',
                    Collection::class,
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
    public function reverseTransform($value): Collection
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
