<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($array)
    {
        return $array[0];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        return [$string];
    }
}
