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
        if (count($array) > 0) {
            return $array[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        if (!is_null($string)) {
            return [$string];
        }

        return [];
    }
}
