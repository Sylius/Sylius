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

use Doctrine\Common\Collections\Collection;
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
    private $decoratedTransformer;

    /**
     * @param DataTransformerInterface $decoratedTransformer
     */
    public function __construct(DataTransformerInterface $decoratedTransformer)
    {
        $this->decoratedTransformer = $decoratedTransformer;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($values)
    {
        Assert::isInstanceOf($values, Collection::class);

        return $values->map(function ($value) {
            return $this->decoratedTransformer->transform($value);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($values)
    {
        Assert::isInstanceOf($values, Collection::class);

        return $values->map(function ($value) {
            return $this->decoratedTransformer->reverseTransform($value);
        });
    }
}
