<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

final class UserVerifiedAtToBooleanTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        return (bool) $value;
    }

    public function reverseTransform($value)
    {
        return $value ? new \DateTime() : null;
    }
}
