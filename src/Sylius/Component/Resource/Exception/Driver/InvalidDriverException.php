<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Exception\Driver;

/**
 * @author Arnaud Langlade <aRn0D.dev@gmail.com>
 */
class InvalidDriverException extends \Exception
{
    public function __construct($driver, $className)
    {
        parent::__construct(sprintf(
            'Driver "%s" is not supported by %s.',
            $driver,
            $className
        ));
    }
}
