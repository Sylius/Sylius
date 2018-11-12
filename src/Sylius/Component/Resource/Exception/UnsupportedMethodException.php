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

class UnsupportedMethodException extends \Exception
{
    public function __construct(string $methodName)
    {
        parent::__construct(sprintf(
            'The method "%s" is not supported.',
            $methodName
        ));
    }
}
