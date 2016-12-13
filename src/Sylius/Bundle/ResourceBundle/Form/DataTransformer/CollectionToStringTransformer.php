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
use Webmozart\Assert\Assert;

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
        Assert::isInstanceOf($values, Collection::class);
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
        Assert::string($value);
        if ('' === $value) {
            return new ArrayCollection();
        }

        return new ArrayCollection(explode($this->delimiter, $value));
    }
}
