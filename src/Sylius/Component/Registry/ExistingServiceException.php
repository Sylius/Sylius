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

namespace Sylius\Component\Registry;

/**
 * This exception should be thrown by service registry
 * when given type already exists.
 */
class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type)
    {
        parent::__construct(sprintf('%s of type "%s" already exists.', $context, $type));
    }
}
