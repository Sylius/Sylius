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

namespace Sylius\Component\Resource\Exception;

class UnexpectedTypeException extends \InvalidArgumentException
{
    /**
     * @param mixed $value
     */
    public function __construct($value, string $expectedType)
    {
        parent::__construct(sprintf(
            'Expected argument of type "%s", "%s" given.',
            $expectedType,
            is_object($value) ? get_class($value) : gettype($value)
        ));
    }
}
